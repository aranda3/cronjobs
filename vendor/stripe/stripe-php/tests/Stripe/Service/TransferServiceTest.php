<?php

namespace Stripe\Service;

/**
 * @internal
 *
 * @covers \Stripe\Service\TransferService
 */
final class TransferServiceTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    const TEST_RESOURCE_ID = 'tr_123';
    const TEST_REVERSAL_ID = 'trr_123';

    /** @var \Stripe\StripeClient */
    private $client;

    /** @var TransferService */
    private $service;

    /**
     * @before
     */
    protected function setUpService()
    {
        $this->client = new \Stripe\StripeClient(['api_key' => 'sk_test_123', 'api_base' => MOCK_URL]);
        $this->service = new TransferService($this->client);
    }

    public function testAll()
    {
        $this->expectsRequest(
            'get',
            '/v1/transfers'
        );
        $resources = $this->service->all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(\Stripe\Transfer::class, $resources->data[0]);
    }

    public function testAllReversals()
    {
        $this->expectsRequest(
            'get',
            '/v1/transfers/' . self::TEST_RESOURCE_ID . '/reversals'
        );
        $resources = $this->service->allReversals(self::TEST_RESOURCE_ID);
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(\Stripe\TransferReversal::class, $resources->data[0]);
    }

    public function testCreate()
    {
        $this->expectsRequest(
            'post',
            '/v1/transfers'
        );
        $resource = $this->service->create([
            'amount' => 100,
            'currency' => 'usd',
            'destination' => 'acct_123',
        ]);
        self::assertInstanceOf(\Stripe\Transfer::class, $resource);
    }

    public function testCreateReversal()
    {
        $this->expectsRequest(
            'post',
            '/v1/transfers/' . self::TEST_RESOURCE_ID . '/reversals'
        );
        $resource = $this->service->createReversal(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\TransferReversal::class, $resource);
    }

    public function testRetrieve()
    {
        $this->expectsRequest(
            'get',
            '/v1/transfers/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\Transfer::class, $resource);
    }

    public function testRetrieveReversal()
    {
        $this->expectsRequest(
            'get',
            '/v1/transfers/' . self::TEST_RESOURCE_ID . '/reversals/' . self::TEST_REVERSAL_ID
        );
        $resource = $this->service->retrieveReversal(self::TEST_RESOURCE_ID, self::TEST_REVERSAL_ID);
        self::assertInstanceOf(\Stripe\TransferReversal::class, $resource);
    }

    public function testUpdate()
    {
        $this->expectsRequest(
            'post',
            '/v1/transfers/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(\Stripe\Transfer::class, $resource);
    }

    public function testUpdateReversal()
    {
        $this->expectsRequest(
            'post',
            '/v1/transfers/' . self::TEST_RESOURCE_ID . '/reversals/' . self::TEST_REVERSAL_ID
        );
        $resource = $this->service->updateReversal(
            self::TEST_RESOURCE_ID,
            self::TEST_REVERSAL_ID,
            [
                'metadata' => ['key' => 'value'],
            ]
        );
        self::assertInstanceOf(\Stripe\TransferReversal::class, $resource);
    }
}
