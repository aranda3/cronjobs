<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\Plan
 */
final class PlanTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'plan';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/plans'
        );
        $resources = Plan::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(Plan::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/plans/' . self::TEST_RESOURCE_ID
        );
        $resource = Plan::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(Plan::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/plans'
        );
        $resource = Plan::create([
            'amount' => 100,
            'interval' => 'month',
            'currency' => 'usd',
            'nickname' => self::TEST_RESOURCE_ID,
            'id' => self::TEST_RESOURCE_ID,
        ]);
        self::assertInstanceOf(Plan::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Plan::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata['key'] = 'value';
        $this->expectsRequest(
            'post',
            '/v1/plans/' . $resource->id
        );
        $resource->save();
        self::assertInstanceOf(Plan::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/plans/' . self::TEST_RESOURCE_ID
        );
        $resource = Plan::update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(Plan::class, $resource);
    }

    public function testIsDeletable()
    {
        $resource = Plan::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/plans/' . $resource->id
        );
        $resource->delete();
        self::assertInstanceOf(Plan::class, $resource);
    }
}
