<?php


namespace Nkaurelien\Momopay\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;

/**
 * Class MomoTransaction
 * @package Nkaurelien\Momopay\Models
 *
 * @property-read  int id
 * @property string reference_id
 * @property string transaction_id
 * @property string transaction_status
 * @property Carbon verified_at
 * @property array payment_result
 * @method static Builder toVerify
 */
class MomoTransaction extends Model
{

    protected $table = 'momo_transactions';

    protected $hidden = ['payment_result'];
    protected $guarded = [];
    protected $casts = ['payment_result' => 'array', 'verified_at' => 'date'];

    public function scopeToVerify(Builder $query)
    {
        return $query
            ->orWhereNull('verified_at')
            ->orWhereNull('transaction_status')
            ->orWhere('transaction_status', MomoRequestToPayResultDto::STATUS_PENDING);
    }
}