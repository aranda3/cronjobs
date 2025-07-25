<?php

// File generated from our OpenAPI spec

namespace Stripe;

/**
 * Stripe needs to collect certain pieces of information about each account
 * created. These requirements can differ depending on the account's country. The
 * Country Specs API makes these rules available to your integration.
 *
 * You can also view the information from this API call as <a href="/docs/connect/required-verification-information">an online
 * guide</a>.
 *
 * @property string $id Unique identifier for the object. Represented as the ISO country code for this country.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property string $default_currency The default currency for this country. This applies to both payment methods and bank accounts.
 * @property StripeObject $supported_bank_account_currencies Currencies that can be accepted in the specific country (for transfers).
 * @property string[] $supported_payment_currencies Currencies that can be accepted in the specified country (for payments).
 * @property string[] $supported_payment_methods Payment methods available in the specified country. You may need to enable some payment methods (e.g., <a href="https://stripe.com/docs/ach">ACH</a>) on your account before they appear in this list. The <code>stripe</code> payment method refers to <a href="https://stripe.com/docs/connect/destination-charges">charging through your platform</a>.
 * @property string[] $supported_transfer_countries Countries that can accept transfers from the specified country.
 * @property (object{company: (object{additional: string[], minimum: string[]}&StripeObject), individual: (object{additional: string[], minimum: string[]}&StripeObject)}&StripeObject) $verification_fields
 */
class CountrySpec extends ApiResource
{
    const OBJECT_NAME = 'country_spec';

    /**
     * Lists all Country Spec objects available in the API.
     *
     * @param null|array{ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|array|string $opts
     *
     * @return Collection<CountrySpec> of ApiResources
     *
     * @throws Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    /**
     * Returns a Country Spec for a given Country code.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return CountrySpec
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
}
