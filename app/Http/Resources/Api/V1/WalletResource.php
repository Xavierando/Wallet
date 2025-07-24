<?php

namespace App\Http\Resources\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'wallet',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'amount' => $this->amount / 100,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'relationships' => [
                'client' => [
                    'data' => [
                        'type' => 'client',
                        'id' => $this->client_id,
                    ],
                ],
            ],
            'includes' => [
                'transactions' => $this->when(
                    (str_contains($request->input('includes'), 'transactions')
                        && Auth::user()->tokenCan(Abilities::ShowTransaction)),
                    TransactionResource::collection($this->transactions())
                ),
            ],
            'links' => [
                'self' => route('apiv1.wallets.show', ['wallet' => $this->id]),
            ],
        ];
    }
}
