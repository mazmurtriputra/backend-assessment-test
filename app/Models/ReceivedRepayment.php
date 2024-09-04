<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivedRepayment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */

     public const STATUS_PARTIAL = 'partial';
     public const STATUS_REPAID = 'repaid';
 
     public const CURRENCY_IDR = 'IDR';
     public const CURRENCY_SGD = 'SGD';
     public const CURRENCY_THB = 'THB';
     public const CURRENCY_VND = 'VND';
 
     public const CURRENCIES = [
         self::CURRENCY_IDR,
         self::CURRENCY_SGD,
         self::CURRENCY_THB,
         self::CURRENCY_VND,
     ];

     
     
    protected $table = 'received_repayments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id', 
        'amount',
        'due_date',
        'currency_code',
    ];

    /**
     * A Received Repayment belongs to a Loan
     *
     * @return BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
