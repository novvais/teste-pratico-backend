<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->id,
            'client_id' => $this->when(! $request->is('api/client/*'), $this->client_id),
            'amount' => $this->amount,
            'status' => $this->status
        ];
    }
}
