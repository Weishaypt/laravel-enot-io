# Laravel payment processor package for EnotIo gateway

Accept payments via EnotIo ([enot.io](https://enot.io/)) using this Laravel framework package ([Laravel](https://laravel.com)).

- receive payments, adding just the two callbacks

#### Laravel >= 11.*, PHP >= 8.2
> To use the package for Laravel 10.* use the [5.x](https://github.com/Weishaypt/laravel-enot-io/tree/5.x) branch
> 
> To use the package for Laravel 9.* use the [4.x](https://github.com/Weishaypt/laravel-enot-io/tree/4.x) branch
> 
> To use the package for Laravel 8.* use the [3.x](https://github.com/Weishaypt/laravel-enot-io/tree/3.x) branch
> 
> To use the package for Laravel 7.* use the [2.x](https://github.com/Weishaypt/laravel-enot-io/tree/2.x) branch
> 
> To use the package for Laravel 6.* use the [1.x](https://github.com/Weishaypt/laravel-enot-io/tree/1.x) branch

## Installation

Require this package with composer.

``` bash
composer require weishaypt/laravel-enot-io
```

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`

```php
Weishaypt\EnotIo\EnotIoServiceProvider::class,
```

Add the `FreeKassa` facade to your facades array:

```php
'EnotIo' => Weishaypt\EnotIo\Facades\EnotIo::class,
```

Copy the package config to your local config with the publish command:
``` bash
php artisan vendor:publish --provider="Weishaypt\EnotIo\EnotIoServiceProvider"
```

## Configuration

Once you have published the configuration files, please edit the config file in `config/enotio.php`.

- Create an account on [enot.io](enot.io)
- Add your project, copy the `project_id`, `secret_key` and `secret_key_second` params and paste into `config/enotio.php`
- After the configuration has been published, edit `config/enotio.php`
- Set the callback static function for `searchOrder` and `paidOrder`
- Create route to your controller, and call `EnotIo::handle` method
 
## Usage

1) Generate a payment url or get redirect:

```php
$amount = 100; // Payment`s amount

$url = EnotIo::getPayUrl($amount, $order_id);

$redirect = EnotIo::redirectToPayUrl($amount, $order_id);
```

You can add custom fields to your payment:

```php
$rows = [
    'time' => Carbon::now(),
    'info' => 'Local payment'
];

$url = EnotIo::getPayUrl($amount, $order_id, $desc, $payment_methood, $rows);

$redirect = EnotIo::redirectToPayUrl($amount, $order_id, $desc, $payment_methood, $rows);
```

`$desc` and `$payment_methood` can be null.

2) Process the request from EnotIo:
``` php
EnotIo::handle(Request $request)
```

## Important

You must define callbacks in `config/enotio.php` to search the order and save the paid order.


``` php
'searchOrder' => null  // EnotIoController@searchOrder(Request $request)
```

``` php
'paidOrder' => null  // EnotIoController@paidOrder(Request $request, $order)
```

## Example

The process scheme:

1. The request comes from `enot.io` `GET` / `POST` `http://yourproject.com/enotio/result` (with params).
2. The function`EnotIoController@handlePayment` runs the validation process (auto-validation request params).
3. The method `searchOrder` will be called (see `config/enotio.php` `searchOrder`) to search the order by the unique id.
4. If the current order status is NOT `paid` in your database, the method `paidOrder` will be called (see `config/enotio.php` `paidOrder`).

Add the route to `routes/web.php`:
``` php
 Route::get('/enotio/result', 'EnotIoController@handlePayment');
```

> **Note:**
don't forget to save your full route url (e.g. http://example.com/enotio/result ) for your project on [enot.io](enot.io).

Create the following controller: `/app/Http/Controllers/EnotIoController.php`:

``` php
class EnotIoController extends Controller
{
    /**
     * Search the order in your database and return that order
     * to paidOrder, if status of your order is 'paid'
     *
     * @param Request $request
     * @param $order_id
     * @return bool|mixed
     */
    public function searchOrder(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        if($order) {
            $order['_orderSum'] = $order->sum;

            // If your field can be `paid` you can set them like string
            $order['_orderStatus'] = $order['status'];

            // Else your field doesn` has value like 'paid', you can change this value
            $order['_orderStatus'] = ('1' == $order['status']) ? 'paid' : false;

            return $order;
        }

        return false;
    }

    /**
     * When paymnet is check, you can paid your order
     *
     * @param Request $request
     * @param $order
     * @return bool
     */
    public function paidOrder(Request $request, $order)
    {
        $order->status = 'paid';
        $order->save();

        //

        return true;
    }

    /**
     * Start handle process from route
     *
     * @param Request $request
     * @return mixed
     */
    public function handlePayment(Request $request)
    {
        return EnotIo::handle($request);
    }
}
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please send me an email at ya@sanek.dev instead of using the issue tracker.

## Credits

- [Weishaypt](https://github.com/Weishaypt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
