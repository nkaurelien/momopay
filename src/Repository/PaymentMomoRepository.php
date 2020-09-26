<?php


namespace  Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
use  Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;
use Illuminate\Support\Fluent;

class PaymentMomoRepository extends  PaymentMomoSandboxRepository
{

    public function requestToPay (MomoRequestToPayDto $momoRequestToPayDto, $referenceId)
    {

        $url = self::$base_url . "/collection/v1_0/requesttopay";
        $response = \Httpful\Request::post($url, $momoRequestToPayDto->toArray())
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Target-Environment', config('services.mtn.target_environment'))
            ->addHeader('X-Callback-Url', route(config('services.mtn.payment_callback_route')))
            ->addHeader('X-Reference-Id', $referenceId )
            ->addHeader('Authorization', $this->createBearerToken())
//            ->authenticateWithBasic(config('services.mtn.reference_id'), config('services.mtn.api_key'))
            ->send();


        return $this->getPayment($referenceId);
    }

    public function getPayment ($referenceId)
    {

        $url = self::$base_url . "/collection/v1_0/requesttopay/{$referenceId}";
        $response = \Httpful\Request::get($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Target-Environment', config('services.mtn.target_environment'))
            ->addHeader('Authorization', $this->createBearerToken())
            ->send();

        return new MomoRequestToPayResultDto($response->body);
    }

    /**
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public  function createToken ()
    {

        $key = 'MomoAccessToken';

        $tokenCachedData = \Cache::get($key);

        if (!blank($tokenCachedData)) {
            return new MomoToken($tokenCachedData);
        }

        $url = self::$base_url . "/collection/token";
        $response = \Httpful\Request::post($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('Authorization', $this->createBasicAuthorization())
            ->addHeader('Referer', 'https://momodeveloper.mtn.com/docs/services/collection/operations/token-POST/console')
            ->addHeader('Origin', 'https://momodeveloper.mtn.com')
//            ->addHeader('Host', 'momodeveloper.mtn.com')
            ->addHeader('Content-Type', 'application/json')
            ->addHeader('Accept', 'application/json')
//            ->authenticateWithBasic(config('services.mtn.reference_id'), config('services.mtn.api_key'))
            ->send();

        dd('ok', $tokenCachedData, $url, $response, config('services.mtn.reference_id'));
        $token = new MomoToken($response->body);

        \Cache::add($key, $token->toArray(), $token->expirationDate() );

        return $token;
    }

    /**
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public  function createBearerToken ()
    {
        return 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSMjU2In0.eyJjbGllbnRJZCI6IjdhZmJhZGQwLTYxZTMtNDM4OC04NjM5LTg3ZjlmZmI0YTFhZCIsImV4cGlyZXMiOiIyMDIwLTA4LTA3VDE1OjI1OjExLjY4OCIsInNlc3Npb25JZCI6ImEyNjI0MWM3LTg2M2QtNDhjMC1iNWNmLTY0ZTEzYTk4MTJjMiJ9.IfcfOsdW4pzHTtSCSVpjQfgY7SAFojqKARf9GV2nNWFTRrrsR4TC8oM_vkEAQpvy93VbuADrz1Y6lsnLgN_ZhUU3e15zX1w-qT2-Trdbt71AlnFYP9trW_g45ZP_SOXdzUILIiqW4prqT0v9-Yx48BHGxwumPQsx_SfDu4AuLSlI4lUuiHcUHxF46cb0MgYwWNICktwPpA-7FI-Z_ivwlUurf-w8pLrHNCaroyLTfWrHxU3QnJWcIUNRFLfJ1atXQUU8F0i1KVsnIc2795dOW_Ek_fV2GJk2wgUIOPP0l-o8kr3K4SJq8vkxPi4KmxKWtTHVaOMd2yPlJDmxyrMpsw';
//        return 'Bearer ' . $this->createToken()->access_token;
    }

    public  function createBasicAuthorization ()
    {
        $cre = config('services.mtn.reference_id') . ':' . config('services.mtn.api_key');
        return 'Basic ' . base64_encode($cre);
    }


    public function handlePaymentCallbackResult (MomoRequestToPayResultDto $result)
    {
        $json = $result->toJson();
        Log::debug($json);

    }


}
