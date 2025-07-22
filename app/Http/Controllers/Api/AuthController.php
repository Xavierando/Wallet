<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Client;
use App\Models\Emploie;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    public function getUserToken(LoginRequest $request, Client|Emploie $user)
    {
        $credential = $request->validated();

        if ($user && Hash::check($credential['password'], $user->password)) {
            return $this->ok(
                'Authenticated',
                [
                    'token' => $user->createToken(
                        'API token for '.$user->email,
                        Abilities::getAbilities($user),
                        now()->addMonth()
                    )->plainTextToken,
                ]
            );
        }

        return $this->error('Invalid credentials', 401);

    }

    /**
     * Logout
     *
     * Signs out the user and destroy's the API token.
     *
     * @group Authentication
     *
     * @response 200 {}
     */
    public function deleteToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('token destroyed');
    }
}
