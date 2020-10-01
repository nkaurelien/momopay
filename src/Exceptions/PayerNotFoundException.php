<?php


namespace Nkaurelien\Momopay\Exceptions;


class PayerNotFoundException extends MomoPayException
{

    protected $message = "Payee not found.";
    protected $code = "PAYER_NOT_FOUND";
}