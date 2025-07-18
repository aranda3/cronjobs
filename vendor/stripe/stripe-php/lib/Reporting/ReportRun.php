<?php

// File generated from our OpenAPI spec

namespace Stripe\Reporting;

/**
 * The Report Run object represents an instance of a report type generated with
 * specific run parameters. Once the object is created, Stripe begins processing the report.
 * When the report has finished running, it will give you a reference to a file
 * where you can retrieve your results. For an overview, see
 * <a href="https://stripe.com/docs/reporting/statements/api">API Access to Reports</a>.
 *
 * Note that certain report types can only be run based on your live-mode data (not test-mode
 * data), and will error when queried without a <a href="https://stripe.com/docs/keys#test-live-modes">live-mode API key</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string $error If something should go wrong during the run, a message about the failure (populated when <code>status=failed</code>).
 * @property bool $livemode <code>true</code> if the report is run on live mode data and <code>false</code> if it is run on test mode data.
 * @property (object{columns?: string[], connected_account?: string, currency?: string, interval_end?: int, interval_start?: int, payout?: string, reporting_category?: string, timezone?: string}&\Stripe\StripeObject) $parameters
 * @property string $report_type The ID of the <a href="https://stripe.com/docs/reports/report-types">report type</a> to run, such as <code>&quot;balance.summary.1&quot;</code>.
 * @property null|\Stripe\File $result The file object representing the result of the report run (populated when <code>status=succeeded</code>).
 * @property string $status Status of this report run. This will be <code>pending</code> when the run is initially created. When the run finishes, this will be set to <code>succeeded</code> and the <code>result</code> field will be populated. Rarely, we may encounter an error, at which point this will be set to <code>failed</code> and the <code>error</code> field will be populated.
 * @property null|int $succeeded_at Timestamp at which this run successfully finished (populated when <code>status=succeeded</code>). Measured in seconds since the Unix epoch.
 */
class ReportRun extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'reporting.report_run';

    /**
     * Creates a new object and begin running the report. (Certain report types require
     * a <a href="https://stripe.com/docs/keys#test-live-modes">live-mode API key</a>.).
     *
     * @param null|array{expand?: string[], parameters?: array{columns?: string[], connected_account?: string, currency?: string, interval_end?: int, interval_start?: int, payout?: string, reporting_category?: string, timezone?: string}, report_type: string} $params
     * @param null|array|string $options
     *
     * @return ReportRun the created resource
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * Returns a list of Report Runs, with the most recent appearing first.
     *
     * @param null|array{created?: array|int, ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|array|string $opts
     *
     * @return \Stripe\Collection<ReportRun> of ApiResources
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    /**
     * Retrieves the details of an existing Report Run.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return ReportRun
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
