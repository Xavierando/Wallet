<?php

namespace App\Http\Requests\Api\V1;

class TransactionStoreRequest extends TransactionRequest
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
            'data.attributes.from' => 'required|exists:wallets,id',
            'data.attributes.to' => 'required|exists:wallets,id',
            'data.attributes.amount' => 'required|numeric:strict',
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
            'data.attributes.from' => [
                'description' => "The wallets's id starting point (method)",
                'example' => 'No-example',
            ],
            'data.attributes.to' => [
                'description' => "The wallets's id end point (method)",
                'example' => 'No-example',
            ],
            'data.attributes.amount' => [
                'description' => 'numeric amount to transfer',
                'example' => 'No-example',
            ],
        ];
    }
}
