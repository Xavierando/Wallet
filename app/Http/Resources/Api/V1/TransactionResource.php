<?php

namespace App\Http\Resources\Api\V1;

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
            'type' => 'transaction',
            'id' => $this->id,
            'attributes' => [
                'to' => $this->to,
                'from' => $this->from,
                'amount' => $this->amount/100,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at
            ],
            'relationships' => [
                'fromwallet' => [
                    'data' => [
                        'type' => 'wallet',
                        'id' => $this->from
                    ],
                ],
                'towallet' => [
                    'data' => [
                        'type' => 'wallet',
                        'id' => $this->to
                    ]
                ]
            ]
        ];
    }
}
