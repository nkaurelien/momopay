<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property string amount
 * @property string payerMessage
 * @property string externalId
 * @property string payeeNote
 * @property string currency default EUR
 * @property MomoPayerDto payer
 */
class MomoRequestToPayDto extends Fluent
{
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        if (blank($this->payer)) {
            $this->payer =  MomoPayerDto::getIntance();
        }
        if (blank($this->currency)) {
            $this->currency = config('services.mtn.currency');
        }
    }


    public function toArray()
    {
        if ($this->payer !== null) {
            return array_merge($this->attributes, ['payer'=> $this->payer->toArray()] ) ;
        }
        return $this->attributes;
    }
}
