<?php

use App\Models\Employee;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('should filter transaction by wallet id', function () {
    Wallet::factory()->count(50)->create();
    Transaction::factory()->count(1000)->create();

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet, Abilities::ShowTransaction]

    );

    $wallet = Wallet::inRandomOrder()->first();

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['to' => $wallet->id]]));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json->each(fn (AssertableJson $json) => $json->where('attributes.to', $wallet->id)->etc())->etc())->etc()
        );

    $wallet = Wallet::inRandomOrder()->first();

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['from' => $wallet->id]]));

    $response
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json->each(fn (AssertableJson $json) => $json->where('attributes.from', $wallet->id)->etc())->etc())->etc()
        );
});

it('should filter transaction by amount range', function () {
    Wallet::factory()->count(3)->create();
    Transaction::factory()->count(1000)->create();
    Transaction::factory()->count(10)->create(['amount' => 10000]);

    $wallet = Wallet::inRandomOrder()->first();

    $count = Transaction::orWhere([['from', $wallet->id], ['to', $wallet->id]])
        ->where('amount', '>=', 1000)
        ->where('amount', '<=', 60000)
        ->count();

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet, Abilities::ShowTransaction]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['amount' => '10,600']]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter transaction by amount', function () {
    Wallet::factory()->count(3)->create();
    Transaction::factory()->count(1000)->create();
    Transaction::factory()->count(10)->create(['amount' => 10000]);

    $wallet = Wallet::inRandomOrder()->first();

    $countTrue = Transaction::orWhere([['from', $wallet->id], ['to', $wallet->id]])
        ->where('amount', '=', 10000)
        ->count();

    $countFalse = Transaction::orWhere([['from', $wallet->id], ['to', $wallet->id]])
        ->where('amount', '=', 10000000)
        ->count();

    Sanctum::actingAs(

        Employee::Factory()->create(),

        [Abilities::ShowWallet, Abilities::ShowTransaction]

    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['amount' => '100']]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $countTrue);

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['amount' => 100000]]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $countFalse);
});

it('should filter transaction by updateAt', function () {
    Wallet::factory()->count(10)->create();
    $filterDate = now()->subDay();
    $wallet = Wallet::inRandomOrder()->first();
    Transaction::factory()->create(['from' => $wallet->id, 'updated_at' => now()->subDay()]);

    $count = Transaction::whereDate('updated_at', $filterDate)->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet, Abilities::ShowTransaction]
    );

    $response = $this
        ->getJson(route('apiv1.wallets.transactions.index', ['wallet' => $wallet->id, 'filter' => ['updatedAt' => $filterDate->format('Y-m-d')]]));

    $response
        ->assertStatus(200)
        ->assertJsonPath('meta.total', $count);
});

it('should filter transaction by updateAt range', function () {
    $filterDate = array_map(
        fn ($v) => $v->format('Y-m-d'),
        [now()->subDay(3), now()->subDays()]
    );
    Wallet::factory()->count(10)->create();
    $wallet = Wallet::inRandomOrder()->first();
    for ($i = 0; $i < 100; $i++) {
        Transaction::factory()->create(['from' => $wallet->id, 'updated_at' => now()->subSeconds(rand(0, 10 * 24 * 60 * 60))]);
    }

    $count = Transaction::whereBetween(
        'updated_at',
        $filterDate
    )->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet, Abilities::ShowTransaction]
    );

    $response = $this
        ->getJson(
            route(
                'apiv1.wallets.transactions.index',
                [
                    'wallet' => $wallet->id,
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

it('should filter transaction by createdAt range', function () {
    $filterDate = array_map(
        fn ($v) => $v->format('Y-m-d'),
        [now()->subDay(3), now()->subDays()]
    );
    Wallet::factory()->count(10)->create();
    $wallet = Wallet::inRandomOrder()->first();
    for ($i = 0; $i < 100; $i++) {
        Transaction::factory()->create(['from' => $wallet->id, 'created_at' => now()->subSeconds(rand(0, 10 * 24 * 60 * 60))]);
    }

    $count = Transaction::whereBetween(
        'created_at',
        $filterDate
    )->count();

    Sanctum::actingAs(
        Employee::Factory()->create(),
        [Abilities::ShowWallet, Abilities::ShowTransaction]
    );

    $response = $this
        ->getJson(
            route(
                'apiv1.wallets.transactions.index',
                [
                    'wallet' => $wallet->id,
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
