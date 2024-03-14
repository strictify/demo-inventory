<?php

declare(strict_types=1);

namespace App\Command;

use Money\Parser\IntlMoneyParser;
use Money\Currencies\ISOCurrencies;
use Symfony\Component\Panther\Client;
use Money\Formatter\IntlMoneyFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function dump;

#[AsCommand(
    name: 'dummy',
    description: 'Add a short description for your command',
)]
class DummyCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);

        $money = $moneyParser->parse('$362.00');
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        dump($moneyFormatter->format($money));

        return 0;


        $io = new SymfonyStyle($input, $output);

        $client = Client::createChromeClient();
        $crawler = $client->request('GET', 'https://amazon.com/Apple-iPhone-13-128GB-Blue/dp/B09LNX6KQS');

//        $price = $client->waitFor('##corePrice_feature_div')->text();
//        dump($price);
//        return 0;

        $titleCrawler = $crawler->filter('#corePrice_feature_div');

        $title = $titleCrawler->text();
        $client->takeScreenshot('asdsd.png');
        dump($title);

        return 0;
        $first = $crawler->filter('#corePrice_desktop')->first();
        dump($first->html());

        return 0;
        $x = $first->filter('.a-offscreen');

        dump($x->text());

        return 0;

        $titleCrawler = $crawler->filter('span#productTitle');

        $priceCrawler = $client->waitFor('#apex_desktop span.a-price span.a-offscreen');
        $priceCrawler->filter('#apex_desktop span.a-price span.a-offscreen');

        $title = $titleCrawler->text();
        $price = $priceCrawler->text();
        dump($title, $price);

        return Command::SUCCESS;
    }
}
