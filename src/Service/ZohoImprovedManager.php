<?php

declare(strict_types=1);

namespace App\Service;

use Override;
use Generator;
use Throwable;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\Entity\ZohoAwareInterface;
use App\Service\Zoho\Sync\TaxSync;
use App\Turbo\Stream\ReloadStream;
use App\Message\AsyncMessageInterface;
use App\Service\Zoho\Sync\ProductSync;
use App\Entity\Product\ZohoStatusEnum;
use App\Service\Mercure\StreamBuilder;
use App\Service\Zoho\Sync\WarehouseSync;
use App\Service\Zoho\Model\SyncInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Message\Zoho\ZohoSyncEntityMessage;
use App\Message\Zoho\ZohoDownloadAllMessage;
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
use function is_string;

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
        private StreamBuilder $streamBuilder,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @api
     */
    #[AsMessageHandler(handles: ZohoSyncEntityMessage::class)]
    public function handleSyncEntityMessage(ZohoSyncEntityMessage $message): void
    {
        try {
            $this->syncEnabled = false;
            $entityClassName = $message->getClass();
            $sync = $this->findSyncForEntity($entityClassName) ?? throw new UnrecoverableMessageHandlingException(sprintf('Entity %s not supported', $entityClassName));
            $zohoId = $message->getZohoId();
            if (!$entity = $sync->findEntityByZohoId($zohoId)) {
                return;
            }
            $url = $sync->getBaseUrl() . '/' . $zohoId;
            $company = $entity->getCompany();

            match ($message->getAction()) {
                'delete' => $this->zohoClient->delete($company, $url),
                'put' => $this->zohoClient->put($company, $url, data: $sync->createPutPayload($entity)),
                'get' => $this->getOne($entity, $url),
            };
            $entity->setZohoStatus(ZohoStatusEnum::SYNCED);
            $this->companyRepository->flush();
            $this->streamBuilder->pushToApp($company, new ReloadStream(sprintf('app-%s', $entity->getId())));
        } catch (Throwable $e) {
            $this->logger->critical($e->getMessage(), [
                'exception' => $e,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw new UnrecoverableMessageHandlingException(previous: $e);
        } finally {
            $this->syncEnabled = true;
        }
    }

    /**
     * @api
     */
    #[AsMessageHandler(handles: ZohoDownloadAllMessage::class)]
    public function handleDownloadAllMessage(ZohoDownloadAllMessage $message): void
    {
        $id = $message->getId();
        $company = $this->companyRepository->find($id) ?? throw new UnrecoverableMessageHandlingException(sprintf('Company %s not found', $id));
        try {
            $this->downloadAll($company);
        } catch (Throwable $e) {
            throw new UnrecoverableMessageHandlingException(previous: $e);
        }
    }

    #[Override]
    public function reset(): void
    {
        $this->pendingUpdateMessages = [];
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
     * Download all data from Zoho.
     * Temporarily disable sync, we don't want to call Zoho during this process.
     */
    private function downloadAll(Company $company): void
    {
        if (!is_string($company->getZohoAccessToken())) {
            throw new InvalidArgumentException('Not synced with Zoho.');
        }
        try {
            $this->syncEnabled = false;
            $company->setZohoDownloading(true);
            $this->companyRepository->flush();
            $this->streamBuilder->pushToApp($company, new ReloadStream(sprintf('app-%s', $company->getId())));
            foreach ($this->getSyncsInOrder() as $serviceName) {
                $sync = $this->syncs->get($serviceName);
                $mapperClass = $sync->getMappingClass();
                $data = $this->zohoClient->get($company, $sync->getBaseUrl(), $mapperClass);
                $streams = [];
                foreach ($data->getMany() as $item) {
                    $zohoId = $item->getId();
                    $entity = $sync->findEntityByZohoId($zohoId) ?? $sync->createNewEntity($company, $item);
                    $entity->setZohoId($item->getId());
                    $entity->setZohoStatus(ZohoStatusEnum::SYNCED);
                    $sync->map($entity, $item);
                    $streams[] = new ReloadStream(sprintf('app-%s', $entity->getId()));
                }
                // in case tagged service didn't flush its entities, do it here
                $this->companyRepository->flush();
                $this->streamBuilder->pushToApp($company, ...$streams);
            }
        } finally {
            $this->syncEnabled = true;
            $company->setZohoDownloading(false);
            $this->companyRepository->flush();
            $this->streamBuilder->pushToApp($company, new ReloadStream(sprintf('app-%s', $company->getId())));
        }
    }

    /**
     * We have to loop through services to assure correct types are used.
     * PHP doesn't have generics and emulation via psalm still can't match the real thing.
     */
    private function getOne(ZohoAwareInterface $entity, string $url): void
    {
        $company = $entity->getCompany();
        foreach ($this->syncs->getProvidedServices() as $serviceName) {
            $sync = $this->syncs->get($serviceName);
            if (is_a($entity, $sync->getEntityClass(), true)) {
                $mapperClass = $sync->getMappingClass();
                $data = $this->zohoClient->get($company, $url, $mapperClass);
                $item = $data->getOne();
                $sync->map($entity, $item);
                // bail out, no need to lookup anymore
                return;
            }
        }
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
