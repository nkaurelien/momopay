<?php


namespace Nkaurelien\Momopay\Repository;


use  Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
use  Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;
use Illuminate\Support\Fluent;
use function Couchbase\defaultDecoder;

class PaymentMomoRepository extends PaymentMomoSandboxRepository
{

    /**
     * @param MomoRequestToPayDto $momoRequestToPayDto
     * @param $referenceId
     * @return MomoRequestToPayResultDto
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function requestToPay(MomoRequestToPayDto $momoRequestToPayDto, $referenceId)
    {

        $url = self::$BASE_URL . "/collection/v1_0/requesttopay";
        $postRequest = \Httpful\Request::post($url)
            ->body($momoRequestToPayDto->toArray())
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Target-Environment', self::$TARGER_ENVIRONMENT)
            ->addHeader('X-Reference-Id', $referenceId)
            ->addHeader('Authorization', $this->createBearerToken())
            ->expectsJson()
            ->sendsJson();

        $payment_callback_route = config('services.mtn.payment_callback_route');
        if (!blank($payment_callback_route)) {
            $postRequest->addHeader('X-Callback-Url', route($payment_callback_route));
        }

        $response = $postRequest
            ->send();

        return $this->getPayment($referenceId);
    }

    /**
     * @param $referenceId
     * @return MomoRequestToPayResultDto
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getPayment($referenceId)
    {

        $url = self::$BASE_URL . "/collection/v1_0/requesttopay/{$referenceId}";
        $response = \Httpful\Request::get($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Target-Environment', self::$TARGER_ENVIRONMENT)
            ->addHeader('Authorization', $this->createBearerToken())
            ->send();

        return new MomoRequestToPayResultDto($response->body);
    }

    /**
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function createToken()
    {

        $key = 'MomoAccessToken';

        $tokenCachedData = \Cache::get($key);

        if (!blank($tokenCachedData)) {
            return new MomoToken($tokenCachedData);
        }

        $url = self::$BASE_URL . "/collection/token";
        $response = \Httpful\Request::post($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('Authorization', $this->createBasicAuthorization())
//            ->addHeader('Referer', 'https://momodeveloper.mtn.com/docs/services/collection/operations/token-POST/console')
//            ->addHeader('Origin', 'https://momodeveloper.mtn.com')
//            ->addHeader('Host', 'momodeveloper.mtn.com')
            ->addHeader('Content-Type', 'application/json')
            ->addHeader('Accept', 'application/json')
//            ->authenticateWithBasic(config('services.mtn.reference_id'), config('services.mtn.api_key'))
            ->send();

        $token = new MomoToken($response->body);

        \Cache::add($key, $token->toArray(), $token->expirationDate());

        return $token;
    }

    /**
     * @return string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function createBearerToken()
    {
        return 'Bearer ' . $this->createToken()->access_token;
    }

    public function createBasicAuthorization()
    {
        $cre = config('services.mtn.reference_id') . ':' . config('services.mtn.api_key');
        return 'Basic ' . base64_encode($cre);
    }


    public function handlePaymentCallbackResult(MomoRequestToPayResultDto $result)
    {
        $json = $result->toJson();
        Log::debug($json);

    }


}
