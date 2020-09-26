<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoApiKey;
use  Nkaurelien\Momopay\Fluent\MomoApiUser;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;

abstract class PaymentMomoBaseRepository
{
    public static $base_url = null;

    public function __construct()
    {

        // https://ericssonbasicapi1.azure-api.net
        $go_live = config('services.mtn.go_live');
        self::$base_url = $go_live ? 'https://momodeveloper.mtn.com' : 'https://sandbox.momodeveloper.mtn.com';
    }


}
