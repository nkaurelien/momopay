<?php


namespace Nkaurelien\Momopay\Exceptions;


class ResourceNotFound extends \Exception
{

    protected $message = "Requested resource was not found.";
    protected $code = "RESOURCE_NOT_FOUND";
}