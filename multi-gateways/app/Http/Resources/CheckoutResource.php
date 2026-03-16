<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Transaction
 */
class CheckoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'quantity' => data_get($product, 'pivot.quantity')
                ];
            })
        ];
    }
}
