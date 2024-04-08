<?php

declare(strict_types=1);

namespace App\Service\Zoho;

use Override;
use Generator;
use App\Entity\Company\Company;
use App\Message\ZohoSyncMessage;
use App\Service\Zoho\Sync\TaxSync;
use App\Entity\ZohoAwareInterface;
use App\Service\Zoho\Sync\ProductSync;
use App\Message\AsyncMessageInterface;
use App\Service\Zoho\Sync\WarehouseSync;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Repository\Company\CompanyRepository;
use Symfony\Contracts\Service\ResetInterface;
use App\Service\Zoho\Sync\Model\SyncInterface;
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
use function array_keys;

class ZohoManager implements ResetInterface, PreUpdateEventListenerInterface, PostFlushEventListenerInterface
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
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoSyncMessage $message): void
    {
        $id = $message->getId();
        $company = $this->companyRepository->find($id) ?? throw new UnrecoverableMessageHandlingException(sprintf('Company %s does not exist.', $id));
        $this->downloadAll($company);
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
            $sync->downloadAll($company);
        }
        // in case tagged service didn't flush its entities, do it here
        $this->companyRepository->flush();
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

        foreach ($this->getAllSyncServices() as $sync) {
            if (!is_a($entity, $sync->getEntityName(), true)) {
                continue;
            }
            foreach ($sync->onUpdate($entity, $changeSet) as $message) {
                $this->pendingUpdateMessages[] = $message;
            }
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
     * @return Generator<array-key, SyncInterface>
     */
    private function getAllSyncServices(): Generator
    {
        $identifiers = array_keys($this->syncs->getProvidedServices());
        foreach ($identifiers as $identifier) {
            yield $this->syncs->get($identifier);
        }
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
