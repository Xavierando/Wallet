<?php

namespace Database\Seeders;

use App\Enum\ClientTiers;
use App\Enum\EmploiePositions;
use App\Models\Client;
use App\Models\Emploie;
use App\Models\Transaction;
use App\Models\Wallet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Client::factory()->create([
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'tier' => ClientTiers::Diamond,
        ]);

        Emploie::factory()->create([
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'position' => EmploiePositions::Supervisor,
        ]);

        Wallet::factory()->count(100)->create();
        Transaction::factory()->count(100)->create();
    }
}
