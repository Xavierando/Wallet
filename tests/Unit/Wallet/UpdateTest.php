<?php

use App\Models\Client;
use App\Models\Employee;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('update a new wallet as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);

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

    $wallet = Wallet::Find($wallet->id);
    expect($wallet->title)->toBe('new awesom wallet');
    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.relationships.client.data.id', $client->id);
});

it('can not update a own wallet as a client without permission', function () {
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

it('can not update a not own wallet as a client', function () {
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

it('can update a not own wallet as a employee', function () {
    $employee = Employee::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $employee,
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

it('can not update a not own wallet as a employee without permission', function () {
    $employee = Employee::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $employee,
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

it('can not update a new wallet amount as a client', function () {
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

it('can not update a new wallet amount as a employee', function () {
    $employee = Employee::Factory()->create();
    $wallet = Wallet::Factory()->create();
    $amount = $wallet->amount;

    Sanctum::actingAs(
        $employee,
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
