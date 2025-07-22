<?php

use App\Enum\ClientTiers;
use App\Models\Client;
use App\Models\Emploie;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('update a new wallet as a client', function () {
    $client = Client::Factory()->create();
    $wallet = Wallet::Factory()->create(['client_id' => $client->id]);

    Sanctum::actingAs(
        $client,
        [Abilities::UpdateOwnWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ]
            ],
        ]);

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
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ]
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
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ]
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('can update a not own wallet as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $emploie,
        [Abilities::UpdateWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ]
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.relationships.client.data.id', $wallet->client_id);
});

it('can not update a not own wallet as a emploie without permission', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();

    Sanctum::actingAs(
        $emploie,
        []
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                ]
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
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                    'amount' => 1500000,
                ]
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.attributes.amount', $amount/100)
        ->assertJsonPath('data.relationships.client.data.id', $client->id);
});

it('can not update a new wallet amount as a emploie', function () {
    $emploie = Emploie::Factory()->create();
    $wallet = Wallet::Factory()->create();
    $amount = $wallet->amount;

    Sanctum::actingAs(
        $emploie,
        [Abilities::UpdateWallet]
    );

    $response = $this
        ->patchJson(route('apiv1.wallets.update',['wallet' => $wallet->id]), [
            'data' => [
                'attributes' => [
                    'title' => 'new awesom wallet',
                    'amount' => 1500000,
                ]
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.attributes.title', 'new awesom wallet')
        ->assertJsonPath('data.attributes.amount', $amount/100)
        ->assertJsonPath('data.relationships.client.data.id', $wallet->client_id);
});