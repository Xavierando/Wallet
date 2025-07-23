<?php

use App\Models\Client;
use App\Models\Emploie;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('delete a owned wallet as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id, 'amount' => 0]);

    Sanctum::actingAs(
        $client,
        [Abilities::DeleteOwnWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'wallet deleted');
});

it('can not delete a own wallet as a client without permission', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id, 'amount' => 0]);

    Sanctum::actingAs(
        $client,
        []
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

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
        [Abilities::DeleteOwnWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can delete a not own wallet as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create(['amount' => 0]);

    Sanctum::actingAs(
        $emploie,
        [Abilities::DeleteWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'wallet deleted');
});

it('can not delete a not own wallet as a emploie without permission', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create(['amount' => 0]);

    Sanctum::actingAs(
        $emploie,
        []
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can not delete a wallet with a non-zero amount as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id, 'amount' => 10000]);

    Sanctum::actingAs(
        $client,
        [Abilities::DeleteOwnWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Can\'t delete a wallet with a non-zero amount');
});

it('can not delete a wallet with a non-zero amount as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create(['amount' => 10000]);

    Sanctum::actingAs(
        $emploie,
        [Abilities::DeleteWallet]
    );

    $response = $this
        ->deleteJson(route('apiv1.wallets.destroy', ['wallet' => $wallet->id]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Can\'t delete a wallet with a non-zero amount');
});
