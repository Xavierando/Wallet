<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Models\Client;
use App\Traits\ApiResponses;

class AuthClientController extends AuthController
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the client and returns a client's API token.
     *
     * @unauthenticated
     *
     * @group Authentication
     *
     * @request {"email":"myemail@test.tst","password":"mypassword"}
     *
     * @response 200 {"data": {"token": "{YOUR_AUTH_KEY}"},"message": "Authenticated","status": 200}
     */
    public function getToken(LoginRequest $request)
    {
        return $this->getUserToken($request, Client::firstWhere('email', $request->safe()->email));

    }
}
