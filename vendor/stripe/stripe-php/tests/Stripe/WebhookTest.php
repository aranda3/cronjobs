<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\Webhook
 * @covers \Stripe\WebhookSignature
 */
final class WebhookTest extends TestCase
{
    use TestHelper;

    const EVENT_PAYLOAD = '{
  "id": "evt_test_webhook",
  "object": "event",
  "data": { "object": { "id": "rdr_123", "object": "terminal.reader" } }
}';
    const SECRET = 'whsec_test_secret';

    public static function generateHeader($opts = [])
    {
        $timestamp = \array_key_exists('timestamp', $opts) ? $opts['timestamp'] : \time();
        $payload = \array_key_exists('payload', $opts) ? $opts['payload'] : self::EVENT_PAYLOAD;
        $secret = \array_key_exists('secret', $opts) ? $opts['secret'] : self::SECRET;
        $scheme = \array_key_exists('scheme', $opts) ? $opts['scheme'] : WebhookSignature::EXPECTED_SCHEME;
        $signature = \array_key_exists('signature', $opts) ? $opts['signature'] : null;
        if (null === $signature) {
            $signedPayload = "{$timestamp}.{$payload}";
            $signature = \hash_hmac('sha256', $signedPayload, $secret);
        }

        return "t={$timestamp},{$scheme}={$signature}";
    }

    public function testValidJsonAndHeader()
    {
        $sigHeader = $this->generateHeader();
        $event = Webhook::constructEvent(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
        self::assertSame('evt_test_webhook', $event->id);
        self::assertInstanceOf(Terminal\Reader::class, $event->data->__get('object'));
    }

    public function testInvalidJson()
    {
        $this->expectException(Exception\UnexpectedValueException::class);

        $payload = 'this is not valid JSON';
        $sigHeader = $this->generateHeader(['payload' => $payload]);
        Webhook::constructEvent($payload, $sigHeader, self::SECRET);
    }

    public function testValidJsonAndInvalidHeader()
    {
        $this->expectException(Exception\SignatureVerificationException::class);

        $sigHeader = 'bad_header';
        Webhook::constructEvent(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testMalformedHeader()
    {
        $this->expectException(Exception\SignatureVerificationException::class);
        $this->expectExceptionMessage('Unable to extract timestamp and signatures from header');

        $sigHeader = "i'm not even a real signature header";
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testNoSignaturesWithExpectedScheme()
    {
        $this->expectException(Exception\SignatureVerificationException::class);
        $this->expectExceptionMessage('No signatures found with expected scheme');

        $sigHeader = $this->generateHeader(['scheme' => 'v0']);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testNoValidSignatureForPayload()
    {
        $this->expectException(Exception\SignatureVerificationException::class);
        $this->expectExceptionMessage('No signatures found matching the expected signature for payload');

        $sigHeader = $this->generateHeader(['signature' => 'bad_signature']);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET);
    }

    public function testTimestampTooOld()
    {
        $this->expectException(Exception\SignatureVerificationException::class);
        $this->expectExceptionMessage('Timestamp outside the tolerance zone');

        $sigHeader = $this->generateHeader(['timestamp' => \time() - 15]);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10);
    }

    public function testTimestampTooRecent()
    {
        $this->expectException(Exception\SignatureVerificationException::class);
        $this->expectExceptionMessage('Timestamp outside the tolerance zone');

        $sigHeader = $this->generateHeader(['timestamp' => \time() + 15]);
        WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10);
    }

    public function testValidHeaderAndSignature()
    {
        $sigHeader = $this->generateHeader();
        self::assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10));
    }

    public function testHeaderContainsValidSignature()
    {
        $sigHeader = $this->generateHeader() . ',v1=bad_signature';
        self::assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET, 10));
    }

    public function testTimestampOffButNoTolerance()
    {
        $sigHeader = $this->generateHeader(['timestamp' => 12345]);
        self::assertTrue(WebhookSignature::verifyHeader(self::EVENT_PAYLOAD, $sigHeader, self::SECRET));
    }
}
