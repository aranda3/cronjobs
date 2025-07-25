<?php

// File generated from our OpenAPI spec

namespace Stripe\Radar;

/**
 * An early fraud warning indicates that the card issuer has notified us that a
 * charge may be fraudulent.
 *
 * Related guide: <a href="https://stripe.com/docs/disputes/measuring#early-fraud-warnings">Early fraud warnings</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property bool $actionable An EFW is actionable if it has not received a dispute and has not been fully refunded. You may wish to proactively refund a charge that receives an EFW, in order to avoid receiving a dispute later.
 * @property string|\Stripe\Charge $charge ID of the charge this early fraud warning is for, optionally expanded.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $fraud_type The type of fraud labelled by the issuer. One of <code>card_never_received</code>, <code>fraudulent_card_application</code>, <code>made_with_counterfeit_card</code>, <code>made_with_lost_card</code>, <code>made_with_stolen_card</code>, <code>misc</code>, <code>unauthorized_use_of_card</code>.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|string|\Stripe\PaymentIntent $payment_intent ID of the Payment Intent this early fraud warning is for, optionally expanded.
 */
class EarlyFraudWarning extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'radar.early_fraud_warning';

    const FRAUD_TYPE_CARD_NEVER_RECEIVED = 'card_never_received';
    const FRAUD_TYPE_FRAUDULENT_CARD_APPLICATION = 'fraudulent_card_application';
    const FRAUD_TYPE_MADE_WITH_COUNTERFEIT_CARD = 'made_with_counterfeit_card';
    const FRAUD_TYPE_MADE_WITH_LOST_CARD = 'made_with_lost_card';
    const FRAUD_TYPE_MADE_WITH_STOLEN_CARD = 'made_with_stolen_card';
    const FRAUD_TYPE_MISC = 'misc';
    const FRAUD_TYPE_UNAUTHORIZED_USE_OF_CARD = 'unauthorized_use_of_card';

    /**
     * Returns a list of early fraud warnings.
     *
     * @param null|array{charge?: string, created?: array|int, ending_before?: string, expand?: string[], limit?: int, payment_intent?: string, starting_after?: string} $params
     * @param null|array|string $opts
     *
     * @return \Stripe\Collection<EarlyFraudWarning> of ApiResources
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    /**
     * Retrieves the details of an early fraud warning that has previously been
     * created.
     *
     * Please refer to the <a href="#early_fraud_warning_object">early fraud
     * warning</a> object reference for more details.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return EarlyFraudWarning
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
