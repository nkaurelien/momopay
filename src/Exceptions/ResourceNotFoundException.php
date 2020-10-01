<?php


namespace Nkaurelien\Momopay\Exceptions;


class ResourceNotFoundException extends MomoPayException
{

    protected $message = "Requested resource was not found.";
    protected $code = "RESOURCE_NOT_FOUND";
}