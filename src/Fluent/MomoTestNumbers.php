<?php


namespace  Nkaurelien\Momopay\Fluent;


use Illuminate\Support\Fluent;

/**
 * Class MomoToken
 * @package  Nkaurelien\Momopay\Fluent
 */
class MomoTestNumbers extends Fluent
{

    const failed = '46733123450';
    const rejected = '46733123451';
    const timeout = '46733123452';
    const ongoing = '46733123453';
    const pending = '46733123454';
}
