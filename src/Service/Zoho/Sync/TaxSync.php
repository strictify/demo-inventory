<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use App\DTO\Zoho\Taxes;
use App\Entity\Tax\Tax;
use App\Entity\Company\Company;
use App\DTO\Zoho\Tax as ZohoTax;
use App\Service\Zoho\ZohoClient;
use App\Entity\ZohoAwareInterface;
use App\Repository\Tax\TaxRepository;
use App\Message\Zoho\ZohoSyncTaxMessage;
use App\Service\Zoho\Sync\Model\SyncInterface;
use App\Message\Zoho\ZohoSyncWarehouseMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function sprintf;
use function is_string;
use function array_key_exists;

/**
 * @implements SyncInterface<Tax>
 */
class TaxSync implements SyncInterface
{
    public function __construct(
        private ZohoClient $zohoClient,
        private TaxRepository $repository,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoSyncWarehouseMessage $message): void
    {
        if (!$tax = $this->repository->find($message->getId())) {
            return;
        }
        if (!is_string($zohoId = $tax->getZohoId())) {
            return;
        }
        // @see https://www.zoho.com/inventory/api/v1/taxes/#overview
        $url = sprintf('/settings/taxes/%s', $zohoId);

        match ($message->getAction()) {
            'remove' => $this->zohoClient->delete($tax->getCompany(), $url),
            'update' => $this->zohoClient->put($tax->getCompany(), $url, data: [
                'tax_name' => $tax->getName(),
                'tax_percentage' => $tax->getValue(),
            ]),
        };
    }

    #[Override]
    public function getEntityName(): string
    {
        return Tax::class;
    }

    #[Override]
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable
    {
        if (!array_key_exists('name', $changeSet) && !array_key_exists('value', $changeSet)) {
            return;
        }

        yield new ZohoSyncTaxMessage($entity, 'update');
    }

    #[Override]
    public function downloadAll(Company $company): void
    {
        $zohoTaxes = $this->zohoClient->get($company, '/settings/taxes', Taxes::class);
        foreach ($zohoTaxes->taxes as $zohoTax) {
            $tax = $this->getTax($zohoTax, $company);
            $tax->setName($zohoTax->getTaxName());
            $tax->setValue((int)$zohoTax->getTaxPercentage());
        }
        $this->repository->flush();
    }

    private function getTax(ZohoTax $zohoTax, Company $company): Tax
    {
        if ($tax = $this->repository->findOneBy(['zohoId' => $zohoTax->getTaxId()])) {
            return $tax;
        }
        $tax = new Tax($company, $zohoTax->getTaxName(), $zohoTax->getTaxPercentage(), zohoId: $zohoTax->getTaxId());
        $this->repository->persist($tax);

        return $tax;
    }
}
