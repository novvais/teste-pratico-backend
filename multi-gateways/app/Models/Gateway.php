<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionAttempt> $transactionAttempts
 * @property-read int|null $transaction_attempts_count
 * @method static \Database\Factories\GatewayFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gateway withoutTrashed()
 * @mixin \Eloquent
 */
class Gateway extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'priority'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function transactionAttempts(): HasMany
    {
        return $this->hasMany(TransactionAttempt::class);
    }
}
