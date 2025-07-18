<?php

// File generated from our OpenAPI spec

namespace Stripe;

/**
 * To share the contents of a <code>File</code> object with non-Stripe users, you can
 * create a <code>FileLink</code>. <code>FileLink</code>s contain a URL that you can use to
 * retrieve the contents of the file without authentication.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property bool $expired Returns if the link is already expired.
 * @property null|int $expires_at Time that the link expires.
 * @property File|string $file The file object this link points to.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string $url The publicly accessible URL to download the file.
 */
class FileLink extends ApiResource
{
    const OBJECT_NAME = 'file_link';

    use ApiOperations\Update;

    /**
     * Creates a new file link object.
     *
     * @param null|array{expand?: string[], expires_at?: int, file: string, metadata?: null|array<string, string>} $params
     * @param null|array|string $options
     *
     * @return FileLink the created resource
     *
     * @throws Exception\ApiErrorException if the request fails
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * Returns a list of file links.
     *
     * @param null|array{created?: array|int, ending_before?: string, expand?: string[], expired?: bool, file?: string, limit?: int, starting_after?: string} $params
     * @param null|array|string $opts
     *
     * @return Collection<FileLink> of ApiResources
     *
     * @throws Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    /**
     * Retrieves the file link with the given ID.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return FileLink
     *
     * @throws Exception\ApiErrorException if the request fails
     */
    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    /**
     * Updates an existing file link object. Expired links can no longer be updated.
     *
     * @param string $id the ID of the resource to update
     * @param null|array{expand?: string[], expires_at?: null|array|int|string, metadata?: null|array<string, string>} $params
     * @param null|array|string $opts
     *
     * @return FileLink the updated resource
     *
     * @throws Exception\ApiErrorException if the request fails
     */
    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
