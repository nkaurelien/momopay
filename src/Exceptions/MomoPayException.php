<?php


namespace Nkaurelien\Momopay\Exceptions;


use Throwable;

class MomoPayException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}