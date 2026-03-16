<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $transaction_id
 * @property int $gateway_id
 * @property int $card_id
 * @property string $status
 * @property string|null $external_id
 * @property array<array-key, mixed>|null $gateway_res
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Card $card
 * @property-read \App\Models\Gateway $gateway
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereGatewayRes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionAttempt withoutTrashed()
 * @mixin \Eloquent
 */
class TransactionAttempt extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'gateway_id',
        'card_id',
        'status',
        'external_id',
        'gateway_res'
    ];

    protected function casts(): array
    {
        return [
            'gateway_res' => 'array'
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
