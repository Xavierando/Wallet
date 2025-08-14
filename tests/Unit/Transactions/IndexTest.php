<?php

use App\Models\Client;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('have visibility on transactions on own wallets as a client', function () {
    $client = Client::Factory()->create();
    $walletfrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletto = Wallet::Factory()->create();
    Transaction::Factory()->count(5)->create(['from' => $walletfrom->id, 'to' => $walletto->id]);
    Transaction::Factory()->count(5)->create(['from' => $walletto->id, 'to' => $walletfrom->id]);

    Sanctum::actingAs(
        $client,
        [Abilities::ShowOwnTransaction]
    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $walletfrom->id]));

    $response->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', 10)
                ->etc()
        );
});

it('do not have visibility on transactions on not own wallets as a client', function () {
    $client = Client::Factory()->create();
    $walletfrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletto = Wallet::Factory()->create();
    Transaction::Factory()->count(5)->create(['from' => $walletfrom->id, 'to' => $walletto->id]);
    Transaction::Factory()->count(5)->create(['from' => $walletto->id, 'to' => $walletfrom->id]);

    Sanctum::actingAs(
        $client,
        [Abilities::ShowOwnTransaction]
    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $walletto->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('do not have visibility on transactions  on wallets as a employee without authorization', function () {
    $employee = Employee::Factory()->create();
    Wallet::factory()->count(100)->create();
    $wallet = Wallet::Factory()->create();
    Transaction::Factory()->count(5)->create(['from' => $wallet->id]);

    Sanctum::actingAs(

        $employee,

        []

    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('have visibility on transactions on wallets as a employee with authorization', function () {
    $employee = Employee::Factory()->create();
    Wallet::factory()->count(100)->create();
    $wallet = Wallet::Factory()->create();
    Transaction::Factory()->count(10)->create(['from' => $wallet->id]);

    Sanctum::actingAs(

        $employee,

        [Abilities::ShowTransaction]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', 10)
                ->etc()
        );
});
