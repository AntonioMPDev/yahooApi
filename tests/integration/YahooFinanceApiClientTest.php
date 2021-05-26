<?php


namespace App\Tests\integration;


use App\Tests\DatabaseDependantTestCase;

class YahooFinanceApiClientTest extends DatabaseDependantTestCase
{
    /**
     * @test
     * @group integration
     */

    public function the_yahoo_finace_api_returns_the_correct_data()
    {
        // Setup
        // Need YahooFinanceClient
        $yahooFinanceClient = self::$kernel->getContainer()->get('yahoo-finance-api-client');

        // So something
        $response = $yahooFinanceClient->fetchStockProfile('AMRN', 'US');

        $stockProfile = json_decode($response['content']);


        // make assertion
        $this->assertSame('AMRN', $stockProfile->symbol);
        $this->assertSame('Amarin Corporation plc', $stockProfile->shortName);
        $this->assertSame('US', $stockProfile->region);
        $this->assertSame('NasdaqGS', $stockProfile->exchangeName);
        $this->assertSame('USD', $stockProfile->currency);
        $this->assertIsFloat( $stockProfile->price);
        $this->assertIsFloat( $stockProfile->previousClose);
        $this->assertIsFloat( $stockProfile->priceChange);


    }
}