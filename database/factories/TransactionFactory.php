<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tranfert>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => Wallet::inRandomOrder()->first(),
            'to' => Wallet::inRandomOrder()->first(),
            'amount' => fake()->numberBetween(1,10000),
        ];
    }
}
