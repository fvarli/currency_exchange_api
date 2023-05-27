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
        $targetCurrencies = explode(',', $request->query->get('target_currencies'));

        $rates = [];

        foreach ($targetCurrencies as $currency) {
            $rate = $this->redis->get("currency_rate:$currency");

            if (!$rate) {
                // Fetch from database
                $currencyRate = $this->entityManager
                    ->getRepository(CurrencyRate::class)
                    ->findOneBy(['currency' => $currency]);

                if ($currencyRate) {
                    $rate = $currencyRate->getRate();
                    // Store in Redis
                    $this->redis->set("currency_rate:$currency", $rate);
                } else {
                    return new JsonResponse(['error' => 'Currency not found: ' . $currency], 404);
                }
            }

            $rates[$currency] = $rate;
        }

        return new JsonResponse($rates);
    }
}
