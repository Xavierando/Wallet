<?php

use App\Models\Client;
use App\Models\Emploie;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('have visibility on transactions on own wallets as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);
    Wallet::factory()->count(100)->create();

    Sanctum::actingAs(

        $client,

        [Abilities::ShowOwnWallet]

    );

    $response = $this
        ->getJson(route('apiv1.transactions.index'));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', 2)
                ->first(
                    fn (AssertableJson $json) => $json->where('0.relationships.client.data.id', $client->id)
                        ->where('1.relationships.client.data.id', $client->id)
                        ->etc()
                )->etc()
        );
});

it('do not have visibility on not own wallets as a client', function () {
    $client = Client::Factory()->create();
    Wallet::factory()->count(100)->create();

    Sanctum::actingAs(

        $client,

        [Abilities::ShowOwnWallet]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index'));

    $response
        ->assertStatus(200)
        ->assertJsonPath('data', []);
});

it('do not have visibility on wallets as a emploie without authorization', function () {
    $emploie = Emploie::Factory()->create();
    Wallet::factory()->count(10)->create();

    Sanctum::actingAs(

        $emploie,

        []

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index'));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('have visibility on wallets as a emploie with authorization', function () {
    $emploie = Emploie::Factory()->create();
    Wallet::factory()->count(10)->create();

    Sanctum::actingAs(

        $emploie,

        [Abilities::ShowWallet]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index'));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', 10)
                ->etc()
        );
});
