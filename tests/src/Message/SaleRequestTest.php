<?php

namespace ByTIC\Omnipay\Romcard\Tests\Message;

use ByTIC\Omnipay\Romcard\Gateway;
use ByTIC\Omnipay\Romcard\Helper;
use ByTIC\Omnipay\Romcard\Message\CompletePurchaseRequest;
use ByTIC\Omnipay\Romcard\Message\CompletePurchaseResponse;
use ByTIC\Omnipay\Romcard\Message\SaleRequest;
use ByTIC\Omnipay\Romcard\Message\SaleResponse;
use ByTIC\Omnipay\Romcard\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class SaleRequestTest
 * @package ByTIC\Omnipay\Romcard\Tests\Message
 */
class SaleRequestTest extends AbstractTest
{

    public function testSend()
    {
        $client = $this->generateNoSSLClient();
        $request = HttpRequest::createFromGlobals();
        $parameters = require TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'saleParams.php';
        $request->query->replace($parameters);
        $request = new SaleRequest($client, $request);

        $parameters = require TEST_FIXTURE_PATH . '/enviromentParams.php';
        $parameters['endpointUrl'] = (new Gateway())->getEndpointUrl();
        $request->initialize($parameters);

        /** @var CompletePurchaseResponse $response */
        $response = $request->send();

        self::assertInstanceOf(SaleResponse::class, $response);

        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());

        self::assertEquals('0', $response->getCode());
        self::assertEquals('Approved', $response->getMessage());
        self::assertEquals(Helper::TRANSACTION_TYPE_SALE, $response->getTransactionType());
        self::assertEquals('400509625162', $response->getCardReference());
        self::assertEquals('1F4129EAE09A0B25', $response->getTransactionReference());
        self::assertEquals('100006', $response->getTransactionId());
    }

    /**
     * @return CompletePurchaseRequest
     */
    protected function newPurchaseRequest()
    {
        $client = new HttpClient();
        $request = HttpRequest::createFromGlobals();
        $request = new CompletePurchaseRequest($client, $request);
        return $request;
    }
}
