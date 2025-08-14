<?php

use App\Models\Client;
use App\Models\Employee;
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

it('should authenticate a employee', function () {
    $employee = Employee::Factory()->create();

    $response = $this
        ->postJson(
            route('api.employee.login'),
            ['email' => $employee->email, 'password' => 'password']
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'Authenticated');
});

it('should destroy a employee token', function () {
    $employee = Employee::Factory()->create();

    Sanctum::actingAs(
        $employee,
        []
    );

    $response = $this
        ->deleteJson(
            route('api.employee.logout')
        );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'token destroyed');
});

it('should not authenticate an invalid credential', function () {
    $employee = Employee::Factory()->create();

    $response = $this
        ->postJson(
            route('api.employee.login'),
            ['email' => $employee->email.'m', 'password' => 'password']
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
