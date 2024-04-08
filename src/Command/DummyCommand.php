<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\Zoho\Items;
use App\Service\Zoho\ZohoManager;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\Console\Command\Command;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CuyZ\ValinorBundle\Configurator\Attributes\SupportDateFormats;
use CuyZ\ValinorBundle\Configurator\Attributes\AllowSuperfluousKeys;
use function json_decode;

/**
 * @psalm-suppress all
 */
#[AsCommand(
    name: 'dummy',
    description: 'Add a short description for your command',
)]
class DummyCommand extends Command
{
    public function __construct(
        #[SupportDateFormats('Y-m-d', 'Y-m-d H:i'), AllowSuperfluousKeys]
        private TreeMapper $treeMapper,
        private ZohoManager $zohoSync,
        private CompanyRepository $companyRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $data = $this->json();
            $vars = json_decode($data, true);
//            dump($vars);
            $dto = $this->treeMapper->map(Items::class, Source::array($vars)->camelCaseKeys());

            dump($dto);
//
//        $company = $this->companyRepository->findOneBy(['name' => 'Strictify']) ?? throw new LogicException();
//        $this->zohoSync->downloadAll($company);

            return Command::SUCCESS;
        } catch (MappingError $error) {
            dump($error->getMessage());
            $messages = Messages::flattenFromNode(
                $error->node()
            );
            dump($messages);

//            foreach ($messages as $message) {
//                if ($message->code() === 'some_code') {
//                    $message = $message
//                        ->withParameter('some_parameter', 'some custom value')
//                        ->withBody('new message / {message_code} / {some_parameter}');
//                }
//
//                // new message / some_code / some custom value
//                echo $message;
//            }
        }

        return 0;
    }

    private function json(): string
    {
        return <<<JSON
 {
    "code": 0,
    "message": "success",
    "item": {
        "group_id": 4815000000044220,
        "group_name": "Bags",
        "item_id": 4815000000044208,
        "name": "Bags-small",
        "unit": "qty",
        "item_type": "inventory",
        "product_type": "goods",
        "is_taxable": true,
        "tax_id": 4815000000044043,
        "description": "description",
        "tax_name": "Sales",
        "tax_percentage": 12,
        "tax_type": "Service Tax",
        "purchase_account_id": 4815000000035003,
        "purchase_account_name": "Cost of Goods Sold",
        "account_name": "Sales",
        "inventory_account_id": 4815000000035001,
        "attribute_id1": 4815000000044112,
        "attribute_name1": "Small",
        "status": "active",
        "source": "string",
        "rate": 6,
        "pricebook_rate": 6,
        "purchase_rate": 6,
        "reorder_level": 5,
        "initial_stock": 50,
        "initial_stock_rate": 500,
        "vendor_id": 4815000000044080,
        "vendor_name": "Molly",
        "stock_on_hand": 50,
        "available_stock": 2,
        "actual_available_stock": 2,
        "sku": "SK123",
        "upc": 111111111111,
        "ean": 111111111112,
        "isbn": 111111111113,
        "part_number": 111111111114,
        "attribute_option_id1": 4815000000044214,
        "attribute_option_name1": "Small",
        "image_id": 2077500000000002000,
        "image_name": "bag_s.jpg",
        "purchase_description": "Purchase description",
        "image_type": "jpg",
        "item_tax_preferences": [
            {
                "tax_id": 4815000000044043,
                "tax_specification": "intra"
            }
        ],
        "hsn_or_sac": 85423100,
        "sat_item_key_code": "string",
        "unitkey_code": "string",
        "custom_fields": [
            {
                "customfield_id": "46000000012845",
                "value": "Normal"
            }
        ]
    }
} 
JSON;


    }
}
