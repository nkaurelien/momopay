<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoApiKey;
use  Nkaurelien\Momopay\Fluent\MomoApiUser;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;

class PaymentMomoSandboxRepository extends PaymentMomoBaseRepository
{

    public  function createApiUserApiKey ()
    {

        $reference_id = config('services.mtn.reference_id');
        $url = self::$BASE_URL . "/v1_0/apiuser/{$reference_id}/apikey";
        $response = \Httpful\Request::post($url, [
            'providerCallbackHost' => config('services.mtn.app_domain')
        ])
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->send();

        return new MomoApiKey($response->body);
    }

    public  function createApiUser ($reference_id = null, $providerCallbackHost = null)
    {
        $reference_id = $reference_id ?? config('services.mtn.reference_id');
        $providerCallbackHost = $providerCallbackHost ?? route(config('services.mtn.payment_callback_host'));

        $url = self::$BASE_URL . "/v1_0/apiuser/{$reference_id}";
        $response = \Httpful\Request::post($url, [
            'providerCallbackHost' => $providerCallbackHost,
        ])
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Reference-Id', $reference_id)
            ->send();

        return $response->body;
    }

    public  function getApiUser ($reference_id = null)
    {

        $reference_id = $reference_id ?? config('services.mtn.reference_id');
        $url = self::$BASE_URL . "/v1_0/apiuser/{$reference_id}";
        $response = \Httpful\Request::post($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->send();

        return new MomoApiUser($response->body);
    }


}
