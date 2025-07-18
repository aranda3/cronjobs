<?php

namespace Stripe\Util;

/**
 * @internal
 *
 * @covers \Stripe\Util\RequestOptions
 */
final class RequestOptionsTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    public function testParseString()
    {
        $opts = RequestOptions::parse('foo');
        self::assertSame('foo', $opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseStringStrict()
    {
        $this->expectException(\Stripe\Exception\InvalidArgumentException::class);
        $this->compatExpectExceptionMessageMatches('#Do not pass a string for request options.#');

        $opts = RequestOptions::parse('foo', true);
    }

    public function testParseNull()
    {
        $opts = RequestOptions::parse(null);
        self::assertNull($opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayEmpty()
    {
        $opts = RequestOptions::parse([]);
        self::assertNull($opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayWithAPIKey()
    {
        $opts = RequestOptions::parse(
            [
                'api_key' => 'foo',
            ]
        );
        self::assertSame('foo', $opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayWithIdempotencyKey()
    {
        $opts = RequestOptions::parse(
            [
                'idempotency_key' => 'foo',
            ]
        );
        self::assertNull($opts->apiKey);
        self::assertSame(['Idempotency-Key' => 'foo'], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayWithAPIKeyAndIdempotencyKey()
    {
        $opts = RequestOptions::parse(
            [
                'api_key' => 'foo',
                'idempotency_key' => 'foo',
            ]
        );
        self::assertSame('foo', $opts->apiKey);
        self::assertSame(['Idempotency-Key' => 'foo'], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayWithAPIKeyAndUnexpectedKeys()
    {
        $opts = RequestOptions::parse(
            [
                'api_key' => 'foo',
                'foo' => 'bar',
            ]
        );
        self::assertSame('foo', $opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testParseArrayWithAPIKeyAndUnexpectedKeysStrict()
    {
        $this->expectException(\Stripe\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Got unexpected keys in options array: foo');

        $opts = RequestOptions::parse(
            [
                'api_key' => 'foo',
                'foo' => 'bar',
            ],
            true
        );
    }

    public function testParseArrayWithAPIBase()
    {
        $opts = RequestOptions::parse(
            [
                'api_base' => 'https://example.com',
            ]
        );
        self::assertNull($opts->apiKey);
        self::assertSame([], $opts->headers);
        self::assertSame('https://example.com', $opts->apiBase);
    }

    public function testParseWrongType()
    {
        $this->expectException(\Stripe\Exception\InvalidArgumentException::class);

        $opts = RequestOptions::parse(5);
    }

    public function testMerge()
    {
        $baseOpts = RequestOptions::parse(
            [
                'api_key' => 'foo',
                'idempotency_key' => 'foo',
            ]
        );
        $opts = $baseOpts->merge(
            [
                'idempotency_key' => 'bar',
            ]
        );
        self::assertSame('foo', $opts->apiKey);
        self::assertSame(['Idempotency-Key' => 'bar'], $opts->headers);
        self::assertNull($opts->apiBase);
    }

    public function testDiscardNonPersistentHeaders()
    {
        $opts = RequestOptions::parse(
            [
                'stripe_account' => 'foo',
                'stripe_context' => 'foo',
                'idempotency_key' => 'foo',
            ]
        );
        $opts->discardNonPersistentHeaders();
        self::assertSame(['Stripe-Account' => 'foo'], $opts->headers);
    }

    public function testDebugInfo()
    {
        $opts = RequestOptions::parse(['api_key' => 'sk_test_1234567890abcdefghijklmn']);
        $debugInfo = \print_r($opts, true);
        self::compatAssertStringContainsString('[apiKey] => sk_test_********************klmn', $debugInfo);

        $opts = RequestOptions::parse(['api_key' => 'sk_1234567890abcdefghijklmn']);
        $debugInfo = \print_r($opts, true);
        self::compatAssertStringContainsString('[apiKey] => sk_********************klmn', $debugInfo);

        $opts = RequestOptions::parse(['api_key' => '1234567890abcdefghijklmn']);
        $debugInfo = \print_r($opts, true);
        self::compatAssertStringContainsString('[apiKey] => ********************klmn', $debugInfo);

        $opts = RequestOptions::parse([]);
        $debugInfo = \print_r($opts, true);
        self::compatAssertStringContainsString("[apiKey] => \n", $debugInfo);
    }
}
