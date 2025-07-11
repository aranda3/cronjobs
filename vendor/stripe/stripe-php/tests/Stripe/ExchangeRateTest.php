<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\ExchangeRate
 */
final class ExchangeRateTest extends TestCase
{
    use TestHelper;

    public function testIsListable()
    {
        $this->stubRequest(
            'get',
            '/v1/exchange_rates',
            [],
            null,
            false,
            [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'eur',
                        'object' => 'exchange_rate',
                        'rates' => ['usd' => 1.18221],
                    ],
                    [
                        'id' => 'usd',
                        'object' => 'exchange_rate',
                        'rates' => ['eur' => 0.845876],
                    ],
                ],
            ]
        );

        $resources = ExchangeRate::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(ExchangeRate::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->stubRequest(
            'get',
            '/v1/exchange_rates/usd',
            [],
            null,
            false,
            [
                'id' => 'usd',
                'object' => 'exchange_rate',
                'rates' => ['eur' => 0.845876],
            ]
        );
        $resource = ExchangeRate::retrieve('usd');
        self::assertInstanceOf(ExchangeRate::class, $resource);
    }
}
