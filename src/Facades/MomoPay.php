<?php


namespace Nkaurelien\Momopay\Facades;

use Illuminate\Support\Facades\Facade;
use Nkaurelien\Momopay\Fluent\MomoAccountBalanceResultDto;
use Nkaurelien\Momopay\Fluent\MomoRequestToPayDto;
use Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;

/**
 * Class MomoPay
 * @package Nkaurelien\Momopay\Facades
 * @method MomoRequestToPayResultDto requestToPay(MomoRequestToPayDto $momoRequestToPayDto, string  $referenceId)
 * @method MomoRequestToPayResultDto getPayment(string  $referenceId)
 * @method MomoAccountBalanceResultDto getAccountBalance()
 */
class MomoPay extends Facade
{
    const OPERATOR_MTN_MOMO = 'MTN MOBILE MONEY';
    const OPERATOR_ORANGE_MONEY = 'ORANGE MONEY';

    protected static function getFacadeAccessor() {
        return self::class;
    }

}
