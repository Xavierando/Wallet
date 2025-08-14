<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Models\Employee;
use App\Traits\ApiResponses;

class AuthEmployeeController extends AuthController
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the employee and returns a user's API token.
     *
     * @unauthenticated
     *
     * @group Authentication
     *
     * @response 200 {"data": {"token": "{YOUR_AUTH_KEY}"},"message": "Authenticated","status": 200}
     */
    public function getToken(LoginRequest $request)
    {
        return $this->getUserToken($request, Employee::firstWhere('email', $request->safe()->email));
    }
}
