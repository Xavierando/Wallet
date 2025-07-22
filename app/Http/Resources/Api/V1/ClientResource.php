<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'client',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->title,
                'email' => $this->email,
                'email_verified_at' => $this->email_verified_at,
                'tier' => $this->tier,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            'includes' => new WalletResource($this->whenLoaded('wallet')),
        ];
    }
}
