<?php

namespace App\Http\Requests\Api\V1;

class WalletUpdateRequest extends WalletRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => [
                'required',
                'string',
                'min:5',
            ],
            'data.relationships.client.data.id' => 'integer',
        ];
    }

    /**
     * Get the attributes required by the request.
     *
     * @return array<string, string>
     */
    public function bodyParameters()
    {
        return [
            'data.attributes.title' => [
                'description' => "The wallets's title (method)",
                'example' => 'No-example',
            ],
            'data.relationships.client.data.id' => [
                'description' => '<optional> the wallet client id',
                'example' => 'No-example',
            ],
        ];
    }
}
