<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property string financialTransactionId
 * @property string status
 */
class MomoRequestToPayResultDto extends MomoRequestToPayDto
{

    const  STATUT_SUCCESSFUL = 'SUCCESSFUL';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function isSuccessful()
    {
        return $this->status === self::STATUT_SUCCESSFUL;
    }
}
