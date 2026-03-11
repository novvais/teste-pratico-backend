<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'amount',
        'stock'
    ];

    public function transactions(): BelongsToMany
    { 
        return $this->belongsToMany(Transaction::class, 'transaction_product')->withPivot('quantity', 'id');
    }
}
