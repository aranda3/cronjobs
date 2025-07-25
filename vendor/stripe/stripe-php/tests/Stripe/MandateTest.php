<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\Mandate
 */
final class MandateTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'mandate_123';

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/mandates/' . self::TEST_RESOURCE_ID
        );
        $resource = Mandate::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(Mandate::class, $resource);
    }
}
