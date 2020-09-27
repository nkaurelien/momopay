<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property string partyIdType "MSISDN"
 * @property string telephone
 * @property-read  string partyId Le numero de telephone
 */
class MomoPayerDto extends Fluent
{


    public static function getIntance()
    {
        $instance = new static();
        $instance->partyIdType = "MSISDN";
        return $instance;
    }

    public function setTelephoneAttribute($value)
    {
        $this->partyId = $value;
    }

    public function getTelephoneAttribute()
    {
        return $this->partyId;
    }
}
