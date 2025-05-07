<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Payments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'PaymentID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'BookingID',
        'PaymentReference',
        'Amount',
        'PaymentDate',
        'PaymentMethod',
        'TransactionID',
        'Status',
        'GatewayResponse',
        'RefundAmount',
        'RefundDate',
        'RefundReason'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'PaymentDate' => 'datetime',
        'Amount' => 'decimal:2',
        'RefundAmount' => 'decimal:2',
        'RefundDate' => 'datetime',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'BookingID', 'BookingID');
    }
}