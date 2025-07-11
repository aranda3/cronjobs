<?php

namespace Stripe\Radar;

/**
 * @internal
 *
 * @covers \Stripe\Radar\EarlyFraudWarning
 */
final class EarlyFraudWarningTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    const TEST_RESOURCE_ID = 'issfr_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/radar/early_fraud_warnings'
        );
        $resources = EarlyFraudWarning::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(EarlyFraudWarning::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/radar/early_fraud_warnings/' . self::TEST_RESOURCE_ID
        );
        $resource = EarlyFraudWarning::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(EarlyFraudWarning::class, $resource);
    }
}
