<?php

namespace App\Command;

use App\Entity\CurrencyRate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:currency:rates',
    description: 'Fetches currency exchange rates from the Open Exchange Rates API and saves them in the MySQL Database and Redis.',
)]

class CurrencyRatesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private \Redis $redis;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->redis = new \Redis();
        $this->redis->connect('localhost', 6379);
    }

    protected static $defaultName = 'app:currency:rates';

    protected function configure()
    {
        $this
            ->setDescription('Fetches currency exchange rates from the Open Exchange Rates API.')
            ->addArgument('base_currency', InputArgument::REQUIRED, 'The base currency.')
            ->addArgument('target_currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The target currencies (separate multiple names with a space).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch the arguments
        $baseCurrency = $input->getArgument('base_currency');
        $targetCurrencies = $input->getArgument('target_currencies');

        try {
            // Fetch the rates from the API
            $rates = $this->fetchRatesFromAPI($baseCurrency, $targetCurrencies);

            // Save the rates in the database
            $this->saveRatesInDatabase($rates);

            // Save the rates in Redis
            $this->saveRatesInRedis($rates);

            $output->writeln('Currency rates fetched and saved successfully!');
        } catch (\Exception $e) {
            $output->writeln('An error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Fetches the rates from the Open Exchange Rates API.
     */
    private function fetchRatesFromAPI($baseCurrency, $targetCurrencies)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://openexchangerates.org/api/latest.json', [
            'query' => [
                'app_id' => $_ENV['OPEN_EXCHANGE_RATES_APP_ID'], // replace with your app id from Open Exchange Rates
                'base' => $baseCurrency,
                'symbols' => implode(',', $targetCurrencies),
            ]
        ]);

        $rates = json_decode($response->getBody(), true)['rates'];

        return $rates;
    }


    /**
     * Saves the rates into the MySQL Database.
     */
    private function saveRatesInDatabase($rates)
    {
        foreach ($rates as $currency => $rate) {
            $currencyRate = new CurrencyRate();
            $currencyRate->setCurrency($currency);
            $currencyRate->setRate($rate);

            $this->entityManager->persist($currencyRate);
        }
        $this->entityManager->flush();
    }

    private function saveRatesInRedis($rates)
    {
        foreach ($rates as $currency => $rate) {
            $this->redis->set("currency_rate:$currency", $rate);
        }
    }
}
