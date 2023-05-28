<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExchangeRatesControllerTest extends WebTestCase
{
    public function testExchangeRates()
    {
        $client = static::createClient();

        // (1) Send a request to the endpoint
        $client->request('GET', '/api/exchange-rates?base_currency=USD&target_currencies=EUR,GBP,JPY,TRY');

        // (2) Check the response status code and content
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // (3) Check the response body
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        // Assert the response data structure
        $this->assertArrayHasKey('EUR', $responseData);
        $this->assertArrayHasKey('GBP', $responseData);
        $this->assertArrayHasKey('JPY', $responseData);
        $this->assertArrayHasKey('TRY', $responseData);

        // Assert the type of values
        $this->assertIsNumeric($responseData['EUR']);
        $this->assertIsNumeric($responseData['GBP']);
        $this->assertIsNumeric($responseData['JPY']);
        $this->assertIsNumeric($responseData['TRY']);
    }
}
