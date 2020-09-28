<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Nkaurelien\Momopay\Exceptions\MomoPayException;
use Nkaurelien\Momopay\Exceptions\PayerNotFoundException;
use Nkaurelien\Momopay\Exceptions\ResourceNotFoundException;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property string amount
 * @property string payerMessage
 * @property string externalId
 * @property string referenceId
 * @property string payeeNote
 * @property string currency default EUR
 * @property MomoPayerDto payer
 * @property string financialTransactionId
 * @property string status
 * @property string reason
 * @property string code
 */
class MomoRequestToPayResultDto extends Fluent
{

    const  STATUT_SUCCESSFUL = 'SUCCESSFUL';
    const  STATUT_FAILED = 'FAILED';
    const  STATUT_PENDING = 'PENDING';

    const  FAILURE_PAYER_NOT_FOUND = 'PAYER_NOT_FOUND';
    const  FAILURE_RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';


    private static $errorsMessages = [
        self::FAILURE_PAYER_NOT_FOUND => 'Payee not found'
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function isSuccessful()
    {
        return $this->status === self::STATUT_SUCCESSFUL;
    }

    public function isPending()
    {
        return $this->status === self::STATUT_PENDING;
    }

    public function isFailed()
    {
        return $this->status === self::STATUT_FAILED;
    }

    public function isFailureBecausePayerNotFound()
    {
        return $this->code === self::FAILURE_PAYER_NOT_FOUND || $this->reason === self::FAILURE_PAYER_NOT_FOUND;
    }

    public function isFailureBecauseResourceNotFound()
    {
        return $this->code === self::FAILURE_RESOURCE_NOT_FOUND || $this->reason === self::FAILURE_RESOURCE_NOT_FOUND;
    }


    public function detectFailure()
    {
        throw_if(Arr::has($this->toArray(), 'code') && $this->isFailureBecausePayerNotFound(), new PayerNotFoundException());
        throw_if(Arr::has($this->toArray(), 'code') && $this->isFailureBecauseResourceNotFound(), new ResourceNotFoundException());
    }

    public static function errorMessage(string $key)
    {
        return self::$errorsMessages[$key] ?? 'error';
    }


}
