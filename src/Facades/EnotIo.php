<?php

namespace Weishaypt\EnotIo\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string handle(Request $request)
 * @method static string getPayUrl($amount, $order_id, $phone = null, $email = null, $user_parameters = [])
 * @method static string redirectToPayUrl($amount, $order_id, $phone = null, $email = null, $user_parameters = [])
 * @method static string getFormSignature($project_id, $amount, $secret, $order_id)
 *
 * @see \Weishaypt\EnotIo\FreeKassa
 */
class EnotIo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'enotio';
    }
}
