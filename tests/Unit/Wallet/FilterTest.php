<?php

use App\Models\Client;
use App\Models\Employee;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('should filter by client id', function () {
    Wallet::factory()->count(100)->create();
    $client = Client::Factory()->create();
    Wallet::Factory()->count(10)->create(['client_id' => $client->id]);

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index', ['filter' => ['client' => $client->id]]));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', 10)
                ->first(
                    fn (AssertableJson $json) => $json->where('0.relationships.client.data.id', $client->id)
                        ->where('1.relationships.client.data.id', $client->id)
                        ->etc()
                )->etc()
        );
});

it('should filter by amount range', function () {
    Wallet::factory()->count(100)->create();
    $client = Client::Factory()->create();
    Wallet::factory()->create(['amount' => 0]);
    Wallet::factory()->create(['amount' => 1000]);
    Wallet::factory()->create(['amount' => 5000]);

    $count = Wallet::where('amount', '>=', 1000)->where('amount', '<=', 60000)->count();

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index', ['filter' => ['amount' => '10,600']]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter by amount', function () {
    Wallet::factory()->count(100)->create();
    $client = Client::Factory()->create();
    Wallet::factory()->create(['amount' => 1000]);

    $count = Wallet::where('amount', 1000)->count();

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.index', ['filter' => ['amount' => '10']]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter by title', function () {
    Wallet::factory()->count(100)->create();
    $client = Client::Factory()->create();
    $wallet = Wallet::factory()->create();

    $count = Wallet::where('title', $wallet->title)->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet]
    );

    $response = $this
        ->getJson(route('apiv1.wallets.index', ['filter' => ['title' => $wallet->title]]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter by updateAt', function () {
    Wallet::factory()->count(100)->create();
    $client = Client::Factory()->create();
    $filterDate = now()->subDay();
    Wallet::factory()->create(['updated_at' => now()->subDay()]);

    $count = Wallet::whereDate('updated_at', $filterDate)->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet]
    );

    $response = $this
        ->getJson(route('apiv1.wallets.index', ['filter' => ['updatedAt' => $filterDate->format('Y-m-d')]]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter by updateAt range', function () {
    $filterDate = array_map(
        fn ($v) => $v->format('Y-m-d'),
        [now()->subDay(3), now()->subDays()]
    );
    for ($i = 0; $i < 100; $i++) {
        Wallet::factory()->create(['updated_at' => now()->subSeconds(rand(0, 10 * 24 * 60 * 60))]);
    }

    $count = Wallet::whereBetween(
        'updated_at',
        $filterDate
    )->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet]
    );

    $response = $this
        ->getJson(
            route(
                'apiv1.wallets.index',
                [
                    'filter' => [
                        'updatedAt' => array_reduce(
                            $filterDate,
                            fn ($c, $i) => ($c == '') ? $i : $c.','.$i,
                            ''
                        ),
                    ],
                ]
            )
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter by createdAt range', function () {
    $filterDate = array_map(
        fn ($v) => $v->format('Y-m-d'),
        [now()->subDay(3), now()->subDays()]
    );
    for ($i = 0; $i < 100; $i++) {
        Wallet::factory()->create(['created_at' => now()->subSeconds(rand(0, 10 * 24 * 60 * 60))]);
    }

    $count = Wallet::whereBetween(
        'created_at',
        $filterDate
    )->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet]
    );

    $response = $this
        ->getJson(
            route(
                'apiv1.wallets.index',
                [
                    'filter' => [
                        'createdAt' => array_reduce(
                            $filterDate,
                            fn ($c, $i) => ($c == '') ? $i : $c.','.$i,
                            ''
                        ),
                    ],
                ]
            )
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});
