<?php

use App\Models\Client;
use App\Models\Employee;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('store a new wallet as a client', function () {
    $client = Client::Factory()->create();

    Sanctum::actingAs(

        $client,

        [Abilities::CreateOwnWallet]

    );

    $response = $this
        ->postJson(route('apiv1.wallets.store'), [
            'data' => [
                'attributes' => [
                    'title' => 'new wallet',
                ],
                'relationship' => [
                    'client' => [
                        'data' => [
                            'id' => $client->id,
                        ],
                    ],
                ],
            ],
        ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.attributes.title', 'new wallet')
        ->assertJsonPath('data.attributes.amount', 0)
        ->assertJsonPath('data.relationships.client.data.id', $client->id);
});

it('can not store a new wallet as a client with a different id', function () {
    $client = Client::Factory()->create();
    $client2 = Client::Factory()->create();

    Sanctum::actingAs(

        $client2,

        [Abilities::CreateOwnWallet]

    );

    $response = $this
        ->postJson(route('apiv1.wallets.store'), [
            'data' => [
                'attributes' => [
                    'title' => 'new wallet',
                ],
                'relationships' => [
                    'client' => [
                        'data' => [
                            'id' => $client->id,
                        ],
                    ],
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});

it('store a new wallet for a client as Employee', function () {
    $employee = Employee::Factory()->create();
    $client = Client::Factory()->create();

    Sanctum::actingAs(

        $employee,

        [Abilities::CreateWallet]

    );

    $response = $this
        ->postJson(route('apiv1.wallets.store'), [
            'data' => [
                'attributes' => [
                    'title' => 'new wallet',
                ],
                'relationships' => [
                    'client' => [
                        'data' => [
                            'id' => $client->id,
                        ],
                    ],
                ],
            ],
        ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.attributes.title', 'new wallet')
        ->assertJsonPath('data.attributes.amount', 0)
        ->assertJsonPath('data.relationships.client.data.id', $client->id);
});

it('can not store a new wallet for a client as Employee', function () {
    $employee = Employee::Factory()->create();
    $client = Client::Factory()->create();

    Sanctum::actingAs(

        $employee,

        []

    );

    $response = $this
        ->postJson(route('apiv1.wallets.store'), [
            'data' => [
                'attributes' => [
                    'title' => 'new wallet',
                ],
                'relationship' => [
                    'client' => [
                        'data' => [
                            'id' => $client->id,
                        ],
                    ],
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('errors.status', 401)
        ->assertJsonPath('errors.message', 'Unauthorized');
});
