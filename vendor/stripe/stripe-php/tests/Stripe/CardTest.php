<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\Card
 */
final class CardTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'card_123';

    // Because of the wildcard nature of sources, stripe-mock cannot currently
    // reliably return sources of a given type, so we create a fixture manually
    public function createFixture($params = [])
    {
        if (empty($params)) {
            $params['customer'] = 'cus_123';
        }
        $base = [
            'id' => self::TEST_RESOURCE_ID,
            'object' => 'card',
            'metadata' => [],
        ];

        return Card::constructFrom(
            \array_merge($params, $base),
            new Util\RequestOptions()
        );
    }

    public function testHasCorrectUrlForCustomer()
    {
        $resource = $this->createFixture(['customer' => 'cus_123']);
        self::assertSame(
            '/v1/customers/cus_123/sources/' . self::TEST_RESOURCE_ID,
            $resource->instanceUrl()
        );
    }

    public function testHasCorrectUrlForAccount()
    {
        $resource = $this->createFixture(['account' => 'acct_123']);
        self::assertSame(
            '/v1/accounts/acct_123/external_accounts/' . self::TEST_RESOURCE_ID,
            $resource->instanceUrl()
        );
    }

    public function testIsNotDirectlyRetrievable()
    {
        $this->expectException(Exception\BadMethodCallException::class);

        Card::retrieve(self::TEST_RESOURCE_ID);
    }

    public function testIsSaveable()
    {
        $resource = $this->createFixture();
        $resource->metadata['key'] = 'value';
        $this->expectsRequest(
            'post',
            '/v1/customers/cus_123/sources/' . self::TEST_RESOURCE_ID
        );
        $resource->save();
        self::assertSame(Card::class, \get_class($resource));
    }

    public function testIsNotDirectlyUpdatable()
    {
        $this->expectException(Exception\BadMethodCallException::class);

        Card::update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
    }

    public function testIsDeletable()
    {
        $resource = $this->createFixture();
        $this->expectsRequest(
            'delete',
            '/v1/customers/cus_123/sources/' . self::TEST_RESOURCE_ID
        );
        $resource->delete();
        self::assertSame(Card::class, \get_class($resource));
    }
}
