<?php

use App\Models\Client;
use App\Models\Emploie;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('should store a new transaction as a client', function () {
    $client = Client::Factory()->create();
    $walletFrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletTo = Wallet::Factory()->create();
    $amount = 1;

    $amountFrom = $walletFrom->amount;
    $amountTo = $walletTo->amount;

    Sanctum::actingAs(
        $client,
        [Abilities::CreateOwnTransaction]
    );

    $response = $this
        ->postJson(route('apiv1.wallets.transactions.store', ['wallet' => $walletFrom->id]), [
            'data' => [
                'attributes' => [
                    'from' => $walletFrom->id,
                    'to' => $walletTo->id,
                    'amount' => $amount,
                ],
            ],
        ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.type', 'transaction')
        ->assertJsonPath('data.attributes.amount', $amount);

    $walletFrom->refresh();
    $walletTo->refresh();

    expect($walletFrom->amount)->toBe($amountFrom - $amount * 100);
    expect($walletTo->amount)->toBe($amountTo + $amount * 100);
});

it('can not store a new wallet as a client with a different id', function () {
    $client = Client::Factory()->create();
    $walletFrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletTo = Wallet::Factory()->create();
    $amount = 1;

    $amountFrom = $walletFrom->amount;
    $amountTo = $walletTo->amount;

    Sanctum::actingAs(
        Client::Factory()->create(),
        [Abilities::CreateOwnTransaction]
    );

    $response = $this
        ->postJson(route('apiv1.wallets.transactions.store', ['wallet' => $walletFrom->id]), [
            'data' => [
                'attributes' => [
                    'from' => $walletFrom->id,
                    'to' => $walletTo->id,
                    'amount' => $amount,
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('store a new wallet for a client as Emploie', function () {
    $client = Client::Factory()->create();
    $walletFrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletTo = Wallet::Factory()->create();
    $amount = 1;

    $amountFrom = $walletFrom->amount;
    $amountTo = $walletTo->amount;

    Sanctum::actingAs(
        Emploie::Factory()->create(),
        [Abilities::CreateTransaction]
    );

    $response = $this
        ->postJson(route('apiv1.wallets.transactions.store', ['wallet' => $walletFrom->id]), [
            'data' => [
                'attributes' => [
                    'from' => $walletFrom->id,
                    'to' => $walletTo->id,
                    'amount' => $amount,
                ],
            ],
        ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.type', 'transaction')
        ->assertJsonPath('data.attributes.amount', $amount);

    $walletFrom->refresh();
    $walletTo->refresh();

    expect($walletFrom->amount)->toBe($amountFrom - $amount * 100);
    expect($walletTo->amount)->toBe($amountTo + $amount * 100);
});

it('can not store a new wallet for a client as Emploie', function () {
    $client = Client::Factory()->create();
    $walletFrom = Wallet::Factory()->create(['client_id' => $client->id]);
    $walletTo = Wallet::Factory()->create();
    $amount = 1;

    $amountFrom = $walletFrom->amount;
    $amountTo = $walletTo->amount;

    Sanctum::actingAs(
        Emploie::Factory()->create(),
        []
    );

    $response = $this
        ->postJson(route('apiv1.wallets.transactions.store', ['wallet' => $walletFrom->id]), [
            'data' => [
                'attributes' => [
                    'from' => $walletFrom->id,
                    'to' => $walletTo->id,
                    'amount' => $amount,
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});
