<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CurrencyRate;

class ExchangeRatesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private \Redis $redis;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->redis = new \Redis();
        $this->redis->connect('localhost', 6379);
    }

    /**
     * @Route("/api/exchange-rates", name="exchange_rates")
     */
    public function index(Request $request): Response
    {
        $baseCurrency = $request->query->get('base_currency');
        if(!$baseCurrency) {
            return new JsonResponse(['error' => 'base_currency parameter is required.'], 400);
        }

        $targetCurrencies = explode(',', $request->query->get('target_currencies'));
        if(empty($targetCurrencies)) {
            return new JsonResponse(['error' => 'target_currencies parameter is required.'], 400);
        }

        // Validate the currency codes
        $validCurrencies = ['USD', 'EUR', 'GBP', 'JPY']; // Assuming these are the valid ones
        foreach($targetCurrencies as $currency) {
            if(!in_array($currency, $validCurrencies)) {
                return new JsonResponse(['error' => "Invalid currency: $currency"], 400);
            }
        }

        $rates = [];

        foreach ($targetCurrencies as $currency) {
            $rate = $this->redis->get("currency_rate:$currency");
            error_log('Rate from Redis: ' . $rate); // log rate from Redis

            if (!$rate) {
                // Fetch from database
                $currencyRate = $this->entityManager
                    ->getRepository(CurrencyRate::class)
                    ->findOneBy(['currency' => $currency]);

                if ($currencyRate) {
                    $rate = $currencyRate->getRate();
                    error_log('Rate from DB: ' . $rate); // log rate from DB
                    // Store in Redis
                    $this->redis->set("currency_rate:$currency", $rate);
                } else {
                    error_log('Currency not found in DB: ' . $currency); // log missing currency in DB
                    return new JsonResponse(['error' => 'Currency not found: ' . $currency], 404);
                }
            }

            $rates[$currency] = $rate;
        }

        return new JsonResponse($rates);
    }
}
