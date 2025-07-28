<?php

use App\Models\Client;
use App\Models\Emploie;
use Laravel\Sanctum\Sanctum;

it('should authenticate a client', function () {
    $client = Client::Factory()->create();

    $response = $this
        ->postJson(
            route('api.login'),
            ['email' => $client->email, 'password' => 'password']
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'Authenticated');
});

it('should destroy a client token', function () {
    $client = Client::Factory()->create();

    Sanctum::actingAs(
        $client,
        []
    );

    $response = $this
        ->deleteJson(
            route('api.logout')
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'token destroyed');
});

it('should authenticate a emploie', function () {
    $emploie = Emploie::Factory()->create();

    $response = $this
        ->postJson(
            route('api.emploie.login'),
            ['email' => $emploie->email, 'password' => 'password']
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'Authenticated');
});

it('should destroy a emploie token', function () {
    $emploie = Emploie::Factory()->create();

    Sanctum::actingAs(
        $emploie,
        []
    );

    $response = $this
        ->deleteJson(
            route('api.emploie.logout')
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'token destroyed');
});

it('should not authenticate an invalid credential', function () {
    $emploie = Emploie::Factory()->create();

    $response = $this
        ->postJson(
            route('api.emploie.login'),
            ['email' => $emploie->email.'m', 'password' => 'password']
        );

    $response
        ->assertStatus(401)
        ->assertJsonPath('message', 'Invalid credentials');
});

it('should not authenticate a client with invalid credential', function () {
    $client = Client::Factory()->create();

    $response = $this
        ->postJson(
            route('api.login'),
            ['email' => $client->email.'m', 'password' => 'password']
        );

    $response
        ->assertStatus(401)
        ->assertJsonPath('message', 'Invalid credentials');
});
