<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoApiKey;
use  Nkaurelien\Momopay\Fluent\MomoApiUser;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;

abstract class PaymentMomoBaseRepository
{
    public static $BASE_URL = 'https://ericssonbasicapi1.azure-api.net';
    public static $BASE_SANDBOX_URL = 'https://sandbox.momodeveloper.mtn.com';
    public static $TARGER_ENVIRONMENT = 'mtncameroon';

    public function __construct()
    {

        $go_live = config('services.mtn.go_live');
        self::$BASE_URL = $go_live ? self::$BASE_URL : self::$BASE_SANDBOX_URL;
        self::$target_environment = $go_live ? 'mtncameroon' : 'sandbox';

    }


}
