<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\OAuth
 */
final class OAuthTest extends TestCase
{
    use TestHelper;

    public function testAuthorizeUrl()
    {
        $uriStr = OAuth::authorizeUrl([
            'scope' => 'read_write',
            'state' => 'csrf_token',
            'stripe_user' => [
                'email' => 'test@example.com',
                'url' => 'https://example.com/profile/test',
                'country' => 'US',
            ],
        ]);

        $uri = \parse_url($uriStr);
        \parse_str($uri['query'], $params);

        self::assertSame('https', $uri['scheme']);
        self::assertSame('connect.stripe.com', $uri['host']);
        self::assertSame('/oauth/authorize', $uri['path']);

        self::assertSame('ca_123', $params['client_id']);
        self::assertSame('read_write', $params['scope']);
        self::assertSame('test@example.com', $params['stripe_user']['email']);
        self::assertSame('https://example.com/profile/test', $params['stripe_user']['url']);
        self::assertSame('US', $params['stripe_user']['country']);
    }

    public function testRaisesAuthenticationErrorWhenNoClientId()
    {
        $this->expectException(Exception\AuthenticationException::class);
        $this->compatExpectExceptionMessageMatches('#No client_id provided#');

        Stripe::setClientId(null);
        OAuth::authorizeUrl();
    }

    public function testToken()
    {
        $this->stubRequest(
            'POST',
            '/oauth/token',
            [
                'grant_type' => 'authorization_code',
                'code' => 'this_is_an_authorization_code',
            ],
            null,
            false,
            [
                'access_token' => 'sk_access_token',
                'scope' => 'read_only',
                'livemode' => false,
                'token_type' => 'bearer',
                'refresh_token' => 'sk_refresh_token',
                'stripe_user_id' => 'acct_test',
                'stripe_publishable_key' => 'pk_test',
            ],
            200,
            Stripe::$connectBase
        );

        $resp = OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => 'this_is_an_authorization_code',
        ]);
        self::assertSame('sk_access_token', $resp['access_token']);
    }

    public function testDeauthorize()
    {
        $this->stubRequest(
            'POST',
            '/oauth/deauthorize',
            [
                'stripe_user_id' => 'acct_test_deauth',
                'client_id' => 'ca_123',
            ],
            null,
            false,
            [
                'stripe_user_id' => 'acct_test_deauth',
            ],
            200,
            Stripe::$connectBase
        );

        $resp = OAuth::deauthorize([
            'stripe_user_id' => 'acct_test_deauth',
        ]);
        self::assertSame('acct_test_deauth', $resp['stripe_user_id']);
    }
}
