<?php

declare(strict_types=1);

namespace App\Command;

use NumberFormatter;
use Money\Parser\IntlMoneyParser;
use App\Message\ScrapeURLMessage;
use Money\Currencies\ISOCurrencies;
use Symfony\Component\Panther\Client;
use Money\Formatter\IntlMoneyFormatter;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function dump;

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
        private MessageBusInterface $messageBus,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $url = 'https://amazon.com/Apple-iPhone-13-128GB-Blue/dp/B09LNX6KQS';
        $url = 'https://www.amazon.com/Oculus-Quest-Advanced-All-One-Virtual/dp/B099VMT8VZ/ref=sr_1_1?_encoding=UTF8&content-id=amzn1.sym.4b335ede-a344-46a5-af28-95a1242a7034&dib=eyJ2IjoiMSJ9.bIJogqGXSM-VYmE_Sp7eoMRWPlfjJyFXbZzFi4nMkhAbXb29EDb46i42az17NOQdaMqlxakh44OFwEP_CmVNbRljbJqZmwuEcgBLPoFp7QLuMrvhjCsaKKIL_hGgw6B8-DrIOD2hSKr9tJcnROOI9dZEAgs-m0cyx6UzMejPTfxXqpUgR8WB2KOOFoM-vjkgWLcxWHGunoiKOyIQ84_tP8_L_k-6g5JrVeK0AqXvkF0IbsldI8KURdGNfAsPvPBOq_F3mMNzDThrLd7mp3cmvi_aaEADxmuUTIvxi8iP5H8.gnfI9MjBZ7CYHbsVHeXPTwTtf7lucozfEJIReieuE_4&dib_tag=se&keywords=video+gaming&pd_rd_r=d36f6bb4-1a7e-43d4-a80a-97ae3ba483ce&pd_rd_w=sAARE&pd_rd_wg=s4MWg&pf_rd_p=4b335ede-a344-46a5-af28-95a1242a7034&pf_rd_r=1CC5AWBCQ2WK7PPHVX59&qid=1710518859&sr=8-1';
        $this->messageBus->dispatch(new ScrapeURLMessage($url));

        return 0;

        $io = new SymfonyStyle($input, $output);

        $stopwatch = new Stopwatch();
        $stopwatch->start('parser');
        $client = Client::createChromeClient();
        $stopwatch->lap('parser');
        $crawler = $client->request('GET', 'https://amazon.com/Apple-iPhone-13-128GB-Blue/dp/B09LNX6KQS');
        $stopwatch->lap('parser');

        $titleCrawler = $crawler->filter('#corePrice_feature_div');
        $title = $titleCrawler->text();
        $stopwatch->lap('parser');

        $client->takeScreenshot('asdsd.png');

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

    private function extracted(): int
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);

        $money = $moneyParser->parse('$362.00');
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
//        dump($moneyFormatter->format($money));

        return 0;
    }
}
