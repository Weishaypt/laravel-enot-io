<?php

namespace Weishaypt\EnotIo\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Weishaypt\EnotIo\Exceptions\InvalidPaidOrder;
use Weishaypt\EnotIo\Exceptions\InvalidSearchOrder;

trait CallerTrait
{
    /**
     * @param Request $request
     * @return mixed
     *
     * @throws InvalidSearchOrder
     */
    public function callSearchOrder(Request $request)
    {
        if (is_null(config('enotio.searchOrder'))) {
            throw new InvalidSearchOrder();
        }

        return App::call(config('enotio.searchOrder'), ['order_id' => $request->input('merchant_id')]);
    }

    /**
     * @param Request $request
     * @param $order
     * @return mixed
     * @throws InvalidPaidOrder
     */
    public function callPaidOrder(Request $request, $order)
    {
        if (is_null(config('enotio.paidOrder'))) {
            throw new InvalidPaidOrder();
        }

        return App::call(config('enotio.paidOrder'), ['order' => $order]);
    }
}
