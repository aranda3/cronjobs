<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\File
 */
final class FileTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'file_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files'
        );
        $resources = File::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(File::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files/' . self::TEST_RESOURCE_ID
        );
        $resource = File::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(File::class, $resource);
    }

    public function testDeserializesFromFile()
    {
        $obj = Util\Util::convertToStripeObject([
            'object' => 'file',
        ], null);
        self::assertInstanceOf(File::class, $obj);
    }

    public function testDeserializesFromFileUpload()
    {
        $obj = Util\Util::convertToStripeObject([
            'object' => 'file_upload',
        ], null);
        self::assertInstanceOf(File::class, $obj);
    }
}
