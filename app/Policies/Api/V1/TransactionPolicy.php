<?php

namespace App\Policies\Api\V1;

use App\Models\Client;
use App\Models\User;
use App\Models\Wallet;
use App\Permissions\V1\Abilities;

class TransactionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user, $wallet)
    {
        if ($user->tokenCan(Abilities::ShowTransaction)) {
            return true;
        }

        if ($wallet->client_id == $user->id && $user->tokenCan(Abilities::ShowOwnTransaction)) {
            return true;
        }

        return false;
    }

    public function store(User $user, $wallet_id)
    {
        if ($user->tokenCan(Abilities::CreateTransaction)) {
            return true;
        }

        if (
            $user->id == Wallet::findOrFail($wallet_id)->client_id
            && $user::class == Client::class
            && $user->tokenCan(Abilities::CreateOwnTransaction)
        ) {
            return true;
        }

        return false;
    }
}
