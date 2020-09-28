<?php


namespace Nkaurelien\Momopay\Repository;


use Httpful\Response;
use Illuminate\Support\Arr;
use Nkaurelien\Momopay\Events\PaymentAccepted;
use Nkaurelien\Momopay\Exceptions\MomoPayException;
use Nkaurelien\Momopay\Exceptions\PayerNotFoundException;
use Nkaurelien\Momopay\Exceptions\ResourceNotFoundException;
use Nkaurelien\Momopay\Facades\MomoPay;
use Nkaurelien\Momopay\Fluent\MomoAccountBalanceResultDto;
use  Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
use  Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;
use Illuminate\Support\Facades\Log;
use  Nkaurelien\Momopay\Fluent\MomoToken;

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

        $url = self::$BASE_URL . "/collection/v1_0/requesttopay/";
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

        $response = $postRequest->send();

        $this->throwOnResponseError($response);

        $momoRequestToPayResultDto = $this->getPayment($referenceId);

        event(new PaymentAccepted(MomoPay::OPERATOR_MTN_MOMO, $referenceId, $momoRequestToPayResultDto));

        return $momoRequestToPayResultDto;
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

        $this->throwOnResponseError($response);

        $result = new MomoRequestToPayResultDto($response->body);
        $result->referenceId = $referenceId;

        $result->detectFailure();


        return $result;
    }

    /**
     * @return MomoAccountBalanceResultDto
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAccountBalance()
    {

        $url = self::$BASE_URL . "/collection/v1_0/account/balance";
        $response = \Httpful\Request::get($url)
            ->expectsJson()
            ->addHeader('Ocp-Apim-Subscription-Key', config('services.mtn.subscription_key'))
            ->addHeader('X-Target-Environment', self::$TARGER_ENVIRONMENT)
            ->addHeader('Authorization', $this->createBearerToken())
            ->send();

        return new MomoAccountBalanceResultDto($response->body);
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

    /**
     * @param Response $response
     * @throws MomoPayException
     */
    private function throwOnResponseError(Response $response)
    {
        if ($response->code < 200 || $response->code > 399) {
            if ($response->hasBody()) {
                throw new MomoPayException($response->body, $response->code);
            } else {
                throw new MomoPayException('', $response->code);
            }
        }
    }


}
