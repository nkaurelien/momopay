

# MOMOPAY


![Packagist License](https://img.shields.io/packagist/l/nkaurelien/momopay)
![Packagist Version (including pre-releases)](https://img.shields.io/packagist/v/nkaurelien/momopay?include_prereleases)
![Packagist Downloads](https://img.shields.io/packagist/dt/nkaurelien/momopay)

## Description

A package for mobile money payments in Cameroun. <br> Only [Mtn Cameroon](https://mtn.cm/MoMo/) is supported 

## Installation


```shellscript
composer require nkaurelien/momopay
```

(optional) Add the service provider in `config\app.php`

```php 
    providers' => [
        #...
        \Nkaurelien\Momopay\Providers\MomopayServiceProvider::class,
    ]
```
(optional) Add the service facade in `config\app.php`
```php 
    aliases' => [
        #...
        'MomoPay' => \Nkaurelien\Momopay\Facades\MomoPay::class
    ]
```

Run publish
```php 
   php artisan vendor:publish 
```

Run database migration
```php 
   php artisan migrate
```
## Configuration

Add config to `config/services.php`

```text

    'mtn' => [
        'currency' => env('MTN_MOMO_CURRENCY', 'XAF'),
        'go_live' => env('MTN_MOMO_GO_LIVE',false),
        'api_key' => env('MTN_MOMO_USER_API_KEY'),
        'reference_id' => env('MTN_MOMO_ID'),
        'subscription_key' => env('MTN_MOMO_KEY'),
        'payment_callback_route' => env('MTN_MOMO_CALLBACK_URL','payment.momo.callback'),
        'payment_callback_host' => env('MTN_MOMO_CALLBACK_HOST'),
        'notification_email' => env('MTN_MOMO_NOTIFICATION_EMAIL'),
    ],

```

Configuration description

- `reference_id` : is the user id 
- `subscription_key` : is the Ocp-Apim-Subscription-Key
- `target_environment` : can be sanbox or mtncameroon (when you go live)


Don't forget to cache the configurations again with the command `php artisan config:cache`

## Add routes


```php

Route::any('/payment/momo/callback', 'PaymentMomoController@callback')->name('payment.momo.callback');
Route::get('/payment/momo/transaction/{id}', 'PaymentMomoController@getPayment')->name('payment.momo.gettransaction');

```

## Use in controller
First inject the repository class

```php
    private $paymentMomoRepository;
    public function __construct(PaymentMomoRepository $paymentMomoRepository){ #...   
```
Then consume repository instance to implement your payment logic
```php

    $momoRequestToPayDto = new \Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
    $momoRequestToPayDto->amount = 100;
    $momoRequestToPayDto->payeeNote = '';
    $momoRequestToPayDto->payerMessage = '';
    $momoRequestToPayDto->externalId = 'my_product_id';
    $momoRequestToPayDto->payer->telephone = '2376XXXXXXXX';
    
    # optional
    $momoRequestToPayDto->currency = 'XAF'; // Use EUR when you are in sandbox mode

    $refId = \Ramsey\Uuid\Uuid::uuid4()->toString();

    $requestToPayResult = $this->paymentMomoRepository->requestToPay($momoRequestToPayDto, $refId);
```
___
If you prefer the facade instead of injection then do:
```php
    #...
    $requestToPayResult = \Nkaurelien\Momopay\Facades\MomoPay::requestToPay($momoRequestToPayDto, $refId);
```

## Capture events
You can listen to : <br>
- **Nkaurelien\Momopay\Events\PaymentFailed** is fired after a new verification 
- **Nkaurelien\Momopay\Events\PaymentSuccessful** is fired after a new verification 
- **Nkaurelien\Momopay\Events\PaymentAccepted** (pending) is fired after the success of request to pay or a new verification 

## Use commands
To verify transactions do 
````shell script
    php artisan momopay:verify-transactions # to verify all transactions
    php artisan momopay:verify-transactions [referenceId] # to verify a specific transaction
````

## Todo
- [X] Create payment events
- [ ] Create payment exceptions class
- [ ] Create commands for scheduler and cron jobs
- [ ] Create transaction table for re-vefication
- [ ] Email notification on payment success
- [ ] Add orange money payment method

## Useful links
- [MTN MoMo API](https://momodeveloper.mtn.com/)


# Donate
[![Buy Me A Coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.paypal.com/donate/?hosted_button_id=FSXZJUZCHWG5N)

