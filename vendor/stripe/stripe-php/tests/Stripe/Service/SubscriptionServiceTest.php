<?php

namespace Stripe\Service;

/**
 * @internal
 *
 * @covers \Stripe\Service\SubscriptionService
 */
final class SubscriptionServiceTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    const TEST_RESOURCE_ID = 'sub_123';

    /** @var \Stripe\StripeClient */
    private $client;

    /** @var SubscriptionService */
    private $service;

    /**
     * @before
     */
    protected function setUpService()
    {
        $this->client = new \Stripe\StripeClient(['api_key' => 'sk_test_123', 'api_base' => MOCK_URL]);
        $this->service = new SubscriptionService($this->client);
    }

    public function testAll()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscriptions'
        );
        $resources = $this->service->all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(\Stripe\Subscription::class, $resources->data[0]);
    }

    public function testAllPagination()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscriptions'
        );
        $resources = $this->service->all([
            'status' => 'all',
            'limit' => 100,
        ]);
        $filters = $resources->getFilters();
        self::assertSame($filters, [
            'status' => 'all',
            'limit' => 100,
        ]);
    }

    public function testCancel()
    {
        $this->expectsRequest(
            'delete',
            '/v1/subscriptions/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->cancel(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\Subscription::class, $resource);
    }

    public function testCreate()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscriptions'
        );
        $resource = $this->service->create([
            'customer' => 'cus_123',
        ]);
        self::assertInstanceOf(\Stripe\Subscription::class, $resource);
    }

    public function testDeleteDiscount()
    {
        $this->expectsRequest(
            'delete',
            '/v1/subscriptions/' . self::TEST_RESOURCE_ID . '/discount'
        );
        $resource = $this->service->deleteDiscount(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\Discount::class, $resource);
        self::assertTrue($resource->isDeleted());
    }

    public function testRetrieve()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscriptions/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\Subscription::class, $resource);
    }

    public function testUpdate()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscriptions/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(\Stripe\Subscription::class, $resource);
    }
}
