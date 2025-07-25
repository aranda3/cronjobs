<?php

namespace Stripe\Terminal;

/**
 * @internal
 *
 * @covers \Stripe\Terminal\Reader
 */
final class ReaderTest extends \Stripe\TestCase
{
    use \Stripe\TestHelper;

    const TEST_RESOURCE_ID = 'rdr_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/terminal/readers'
        );
        $resources = Reader::all();
        self::compatAssertIsArray($resources->data);
        self::assertInstanceOf(Reader::class, $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        self::assertInstanceOf(Reader::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        $resource->label = 'new-name';

        $this->expectsRequest(
            'post',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource->save();
        self::assertInstanceOf(Reader::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID,
            ['label' => 'new-name']
        );
        $resource = Reader::update(self::TEST_RESOURCE_ID, [
            'label' => 'new-name',
        ]);
        self::assertInstanceOf(Reader::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/terminal/readers',
            ['registration_code' => 'a-b-c']
        );
        $resource = Reader::create(['registration_code' => 'a-b-c']);
        self::assertInstanceOf(Reader::class, $resource);
    }

    public function testIsDeletable()
    {
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource->delete();
        self::assertInstanceOf(Reader::class, $resource);
    }
}
