<?php


namespace Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 * @property string access_token
 * @property string token_type
 * @property int expires_in
 */
class MomoToken extends Fluent
{


    public function expirationDate()
    {
        return now()->addSeconds($this->expires_in);
    }
}
