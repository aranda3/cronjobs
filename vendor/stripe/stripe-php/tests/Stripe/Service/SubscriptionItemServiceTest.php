<?php

namespace Stripe\Service;

/**
 * @internal
 *
 * @covers \Stripe\Service\SubscriptionItemService
 */
final class SubscriptionItemServiceTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    const TEST_RESOURCE_ID = 'si_123';

    /** @var \Stripe\StripeClient */
    private $client;

    /** @var SubscriptionItemService */
    private $service;

    /**
     * @before
     */
    protected function setUpService()
    {
        $this->client = new \Stripe\StripeClient(['api_key' => 'sk_test_123', 'api_base' => MOCK_URL]);
        $this->service = new SubscriptionItemService($this->client);
    }

    public function testAll()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscription_items',
            [
                'subscription' => 'sub_123',
            ]
        );
        $resources = $this->service->all([
            'subscription' => 'sub_123',
        ]);
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(\Stripe\SubscriptionItem::class, $resources->data[0]);
    }

    public function testRetrieve()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscription_items/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\SubscriptionItem::class, $resource);
    }

    public function testCreate()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscription_items'
        );
        $resource = $this->service->create([
            'price' => 'price_123',
            'subscription' => 'sub_123',
        ]);
        self::assertInstanceOf(\Stripe\SubscriptionItem::class, $resource);
    }

    public function testUpdate()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscription_items/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(\Stripe\SubscriptionItem::class, $resource);
    }

    public function testDelete()
    {
        $this->expectsRequest(
            'delete',
            '/v1/subscription_items/' . self::TEST_RESOURCE_ID
        );
        $resource = $this->service->delete(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(\Stripe\SubscriptionItem::class, $resource);
        self::assertTrue($resource->isDeleted());
    }
}
