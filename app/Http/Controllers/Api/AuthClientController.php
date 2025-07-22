<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Client;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthClientController extends AuthController
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the client and returns the client's API token.
     *
     * @unauthenticated
     *
     * @group Authentication
     *
     * @response 200 {"data": {"token": "{YOUR_AUTH_KEY}"},"message": "Authenticated","status": 200}
     */
    public function getToken(LoginRequest $request)
    {
        return $this->getUserToken($request , Client::firstWhere('email', $request->safe()->email));


    }
}