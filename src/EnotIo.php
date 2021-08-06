<?php

namespace Weishaypt\EnotIo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Weishaypt\EnotIo\Traits\CallerTrait;
use Weishaypt\EnotIo\Traits\ValidateTrait;

class EnotIo
{
    use ValidateTrait;
    use CallerTrait;

    //

    /**
     * EnotIo constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $amount
     * @param $order_id
     * @param null $desc
     * @param null $payment_method
     * @param array $user_parameters
     * @return string
     */
    public function getPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
    {
        // Url to init payment on EnotIo
        $url = config('enotio.pay_url');

        // Array of url query
        $query = [];

        // If user parameters array doesn`t empty
        // add parameters to payment query
        if (! empty($user_parameters)) {
            foreach ($user_parameters as $parameter => $value) {
                $query['cf'][$parameter] = $value;
            }
        }

        // Project id (merchat id)
        $query['m'] = config('enotio.project_id');

        // Amount of payment
        $query['oa'] = $amount;

        // Order id
        $query['o'] = $order_id;

        // Payment description (optional)
        if (! is_null($desc)) {
            $query['c'] = $desc;
        }

        // Payment Method (optional)
        if (! is_null($payment_method)) {
            $query['p'] = $payment_method;
        }

        // Payment currency
        if (! is_null(config('enotio.currency'))) {
            $query['cr'] = config('enotio.currency');
        }

        // Payment success_url
        if (! is_null(config('enotio.success_url'))) {
            $query['success_url'] = config('enotio.success_url');
        }

        // Payment fail_url
        if (! is_null(config('enotio.fail_url'))) {
            $query['fail_url'] = config('enotio.fail_url');
        }

        $query['s'] = $this->getFormSignature(
            config('enotio.project_id'),
            $amount,
            config('enotio.secret_key'), $order_id
        );

        // Merge url ang query and return
        return $url.'?'.http_build_query($query);
    }

    /**
     * @param $amount
     * @param $order_id
     * @param null $desc
     * @param null $payment_method
     * @param array $user_parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
    {
        return redirect()->away($this->getPayUrl($amount, $order_id, $desc, $payment_method, $user_parameters));
    }

    /**
     * @param $project_id
     * @param $amount
     * @param $secret
     * @param $order_id
     * @return string
     */
    public function getFormSignature($project_id, $amount, $secret, $order_id)
    {
        $hashStr = $project_id.':'.$amount.':'.$secret.':'.$order_id;

        return md5($hashStr);
    }

    /**
     * @param $project_id
     * @param $amount
     * @param $secret
     * @param $order_id
     * @return string
     */
    public function getSignature($project_id, $amount, $secret, $order_id)
    {
        $hashStr = $project_id.':'.$amount.':'.$secret.':'.$order_id;

        return md5($hashStr);
    }

    /**
     * @param Request $request
     * @return string
     * @throws Exceptions\InvalidPaidOrder
     * @throws Exceptions\InvalidSearchOrder
     */
    public function handle(Request $request)
    {
        // Validate request from FreeKassa
        if (! $this->validateOrderFromHandle($request)) {
            return $this->responseError('validateOrderFromHandle');
        }

        // Search and get order
        $order = $this->callSearchOrder($request);

        if (! $order) {
            return $this->responseError('searchOrder');
        }

        // If order already paid return success
        if (Str::lower($order['_orderStatus']) === 'paid') {
            return $this->responseYES();
        }

        // PaidOrder - update order info
        // if return false then return error
        if (! $this->callPaidOrder($request, $order)) {
            return $this->responseError('paidOrder');
        }

        // Order is paid and updated, return success
        return $this->responseYES();
    }

    /**
     * @param $error
     * @return string
     */
    public function responseError($error)
    {
        return config('enotio.errors.'.$error, $error);
    }

    /**
     * @return string
     */
    public function responseYES()
    {
        // Must return 'YES' if paid successful

        return 'YES';
    }
}
