<?php

namespace Stripe;

/**
 * @internal
 *
 * @covers \Stripe\FileLink
 */
final class FileLinkTest extends TestCase
{
    use TestHelper;

    const TEST_RESOURCE_ID = 'link_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/file_links'
        );
        $resources = FileLink::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(FileLink::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/file_links/' . self::TEST_RESOURCE_ID
        );
        $resource = FileLink::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(FileLink::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/file_links'
        );
        $resource = FileLink::create([
            'file' => 'file_123',
        ]);
        self::assertInstanceOf(FileLink::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = FileLink::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata['key'] = 'value';
        $this->expectsRequest(
            'post',
            '/v1/file_links/' . $resource->id
        );
        $resource->save();
        self::assertInstanceOf(FileLink::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/file_links/' . self::TEST_RESOURCE_ID
        );
        $resource = FileLink::update(self::TEST_RESOURCE_ID, [
            'metadata' => ['key' => 'value'],
        ]);
        self::assertInstanceOf(FileLink::class, $resource);
    }
}
