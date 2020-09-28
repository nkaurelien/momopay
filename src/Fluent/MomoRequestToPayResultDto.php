<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;
use Nkaurelien\Momopay\Exceptions\PayerNotFoundException;
use Nkaurelien\Momopay\Exceptions\ResourceNotFoundException;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property-read string amount
 * @property-read string payerMessage
 * @property-read string externalId
 * @property string referenceId
 * @property-read string payeeNote
 * @property-read string currency default EUR
 * @property-read MomoPayerDto payer
 * @property-read string financialTransactionId
 * @property-read string status
 * @property-read string reason
 * @property-read string code
 */
class MomoRequestToPayResultDto extends Fluent
{

    const  STATUS_SUCCESSFUL = 'SUCCESSFUL';
    const  STATUS_FAILED = 'FAILED';
    const  STATUS_PENDING = 'PENDING';

    const  FAILURE_PAYER_NOT_FOUND = 'PAYER_NOT_FOUND';
    const  FAILURE_RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';


    private static $errorsMessages = [
        self::FAILURE_PAYER_NOT_FOUND => 'Payee not found'
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isSuccessful()
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->isPending();
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
        throw_if($this->isFailureBecausePayerNotFound(), new PayerNotFoundException());
        throw_if($this->isFailureBecauseResourceNotFound(), new ResourceNotFoundException());
    }

    public static function errorMessage(string $key)
    {
        return self::$errorsMessages[$key] ?? 'error';
    }
}
