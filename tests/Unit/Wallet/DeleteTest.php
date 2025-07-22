<?php

use App\Models\Client;
use App\Models\Emploie;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('delete a owned as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id, 'amount' => 0]);

    Sanctum::actingAs(
        $client,
        [Abilities::DeleteOwnWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    var_dump($response->json());

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'wallet deleted');
});

it('can not delete a own wallet as a client without permission', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);

    Sanctum::actingAs(
        $client,
        []
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can not delete a not own wallet as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $client,
        [Abilities::UpdateOwnWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can delete a not own wallet as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $emploie,
        [Abilities::UpdateWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.relationships.client.data.id', $wallet->client_id);
});

it('can not delete a not own wallet as a emploie without permission', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $emploie,
        []
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can not delete a wallet with a non-zero amount as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);
    $amount = $wallet->amount;

    Sanctum::actingAs(
        $client,
        [Abilities::UpdateOwnWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                    'amount' => 1500000,
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.attributes.amount', $amount / 100)
        ->assertJsonPath('data.relationships.client.data.id', $client->id);
});

it('can not delete a wallet with a non-zero amount as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();
    $amount = $wallet->amount;

    Sanctum::actingAs(
        $emploie,
        [Abilities::UpdateWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update', ['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                    'amount' => 1500000,
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.attributes.amount', $amount / 100)
        ->assertJsonPath('data.relationships.client.data.id', $wallet->client_id);
});
