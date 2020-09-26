<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoApiKey;
use  Nkaurelien\Momopay\Fluent\MomoApiUser;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;

abstract class PaymentMomoBaseRepository
{
//    public static $base_url = 'https://momodeveloper.mtn.com';
    public static $base_url = 'https://ericssonbasicapi1.azure-api.net';
    public static $base_sandbox_url = 'https://sandbox.momodeveloper.mtn.com';

    public function __construct()
    {

        $go_live = config('services.mtn.go_live');
        self::$base_url = $go_live ? self::$base_url : self::$base_sandbox_url;
    }


}
