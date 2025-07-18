<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\TaxRate
 */
final class TaxRateTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'txr_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/tax_rates'
        );
        $resources = TaxRate::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(TaxRate::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/tax_rates/' . self::TEST_RESOURCE_ID
        );
        $resource = TaxRate::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(TaxRate::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/tax_rates'
        );
        $resource = TaxRate::create([
            'display_name' => 'name',
            'inclusive' => false,
            'percentage' => 10.15,
        ]);
        self::assertInstanceOf(TaxRate::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = TaxRate::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata['key'] = 'value';
        $this->expectsRequest(
            'post',
            '/v1/tax_rates/' . self::TEST_RESOURCE_ID
        );
        $resource->save();
        self::assertInstanceOf(TaxRate::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/tax_rates/' . self::TEST_RESOURCE_ID
        );
        $resource = TaxRate::update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(TaxRate::class, $resource);
    }
}
