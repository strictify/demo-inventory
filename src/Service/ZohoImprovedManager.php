<?php

declare(strict_types=1);

namespace App\Service;

use Override;
use Generator;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\Entity\ZohoAwareInterface;
use App\Service\Zoho\Sync\TaxSync;
use App\Message\AsyncMessageInterface;
use App\Service\Zoho\Sync\ProductSync;
use App\Service\Zoho\Sync\WarehouseSync;
use App\Service\Zoho\Model\SyncInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Message\Zoho\ZohoPutEntityMessage;
use App\Repository\Company\CompanyRepository;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use App\Autowire\Doctrine\PreUpdateEventListenerInterface;
use App\Autowire\Doctrine\PostFlushEventListenerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use function is_a;
use function sprintf;

class ZohoImprovedManager implements ResetInterface, PreUpdateEventListenerInterface, PostFlushEventListenerInterface
{
    private bool $syncEnabled = true;

    /**
     * @var list<AsyncMessageInterface>
     */
    private array $pendingUpdateMessages = [];

    /**
     * @param ServiceLocator<SyncInterface> $syncs
     */
    public function __construct(
        #[TaggedLocator(tag: SyncInterface::class,)]
        private ServiceLocator $syncs,
        private CompanyRepository $companyRepository,
        private MessageBusInterface $messageBus,
        private ZohoClient $zohoClient,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoPutEntityMessage $message): void
    {
        $entityClassName = $message->getClass();
        $sync = $this->findSyncForEntity($entityClassName) ?? throw new UnrecoverableMessageHandlingException(sprintf('Entity %s not supported', $entityClassName));
        $zohoId = $message->getZohoId();
        if (!$entity = $sync->findEntityByZohoId($zohoId)) {
            return;
        }
        $url = $sync->getBaseUrl() . '/' . $zohoId;
        match ($message->getAction()) {
            'delete' => $this->zohoClient->delete($entity->getCompany(), $url),
            'put' => $this->zohoClient->put($entity->getCompany(), $url, data: $sync->createPutPayload($entity)),
        };
    }

    #[Override]
    public function reset(): void
    {
        $this->pendingUpdateMessages = [];
        $this->syncEnabled = true;
    }

    /**
     * Download all data from Zoho.
     * Temporarily disable sync, we don't want to call Zoho during this process.
     */
    public function downloadAll(Company $company): void
    {
        $this->syncEnabled = false;
        foreach ($this->getSyncsInOrder() as $serviceName) {
            $sync = $this->syncs->get($serviceName);
            $mapperClass = $sync->getMappingClass();
            $data = $this->zohoClient->get($company, $sync->getBaseUrl(), $mapperClass);
            foreach ($data->getMany() as $item) {
                $zohoId = $item->getId();
                $entity = $sync->findEntityByZohoId($zohoId) ?? $sync->createNewEntity($company, $item);
                $sync->map($entity, $item);
            }
            // in case tagged service didn't flush its entities, do it here
            $this->companyRepository->flush();
        }
        $this->syncEnabled = true;
    }

    #[Override]
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        if (!$this->syncEnabled) {
            return;
        }
        $entity = $event->getObject();
        $changeSet = $event->getEntityChangeSet();
        // updates to other entities are of no interest to us
        if (!$entity instanceof ZohoAwareInterface) {
            return;
        }
        if (!$sync = $this->findSyncForEntity($entity)) {
            return;
        }
        foreach ($sync->onUpdate($entity, $changeSet) as $message) {
            $this->pendingUpdateMessages[] = $message;
        }
    }

    #[Override]
    public function postFlush(): void
    {
        foreach ($this->pendingUpdateMessages as $key => $message) {
            $this->messageBus->dispatch($message, [
                new DelayStamp($key * 1_000), // delay each update by 1 second
            ]);
        }
        $this->pendingUpdateMessages = [];
    }

    /**
     * @template TEntity of ZohoAwareInterface
     *
     * @param class-string<TEntity>|TEntity $entityClassName
     */
    private function findSyncForEntity(string|object $entityClassName): SyncInterface|null
    {
        foreach ($this->getSyncsInOrder() as $serviceName) {
            $sync = $this->syncs->get($serviceName);
            if (is_a($entityClassName, $sync->getEntityClass(), true)) {
                return $sync;
            }
        }

        return null;
    }

    /**
     * We need to download data from Zoho in specific order so proper DB relations can be established.
     *
     * @return Generator<array-key, class-string<SyncInterface>>
     */
    private function getSyncsInOrder(): Generator
    {
        yield TaxSync::class;
        yield WarehouseSync::class;
        yield ProductSync::class;
    }
}
