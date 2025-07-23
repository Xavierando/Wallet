<?php

namespace App\Policies\Api\V1;

use App\Models\Client;
use App\Models\Emploie;
use App\Models\User;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class WalletPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user, string $visibility = '')
    {
        if (
            $visibility == 'all'
            && $user->tokenCan(Abilities::ShowWallet)
            && Auth::user()::class == Emploie::class
        ) {
            return true;
        }

        if ($visibility != 'all' && $user->tokenCan(Abilities::ShowOwnWallet)) {
            return true;
        }

    }

    public function show(Client|Emploie $user, Wallet $wallet)
    {
        if ($user->tokenCan(Abilities::ShowOwnWallet) && $wallet->client_id == $user->id) {
            return true;
        }

        if ($user->tokenCan(Abilities::ShowWallet)) {
            return true;
        }

        return false;
    }

    public function store(User $user, $client_id)
    {
        if ($user->tokenCan(Abilities::CreateWallet)) {
            return true;
        }

        if (
            $user->id == $client_id
            && $user::class == Client::class
            && $user->tokenCan(Abilities::CreateOwnWallet)
        ) {
            return true;
        }

        return false;
    }

    public function update(User $user, $wallet)
    {
        if ($user->tokenCan(Abilities::UpdateWallet)) {
            return true;
        }

        if (
            $user->id == $wallet->client_id
            && $user::class == Client::class
            && $user->tokenCan(Abilities::UpdateOwnWallet)
        ) {
            return true;
        }

        return false;
    }

    public function destroy(User $user, $wallet)
    {
        if ($user->tokenCan(Abilities::DeleteWallet)) {
            return true;
        }

        if (
            $user->id == $wallet->client_id
            && $user::class == Client::class
            && $user->tokenCan(Abilities::DeleteOwnWallet)
        ) {
            return true;
        }

        return false;
    }
}
