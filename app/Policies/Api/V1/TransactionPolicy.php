<?php

namespace App\Policies\Api\V1;

use App\Models\Client;
use App\Models\Emploie;
use App\Models\Transaction;
use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class TransactionPolicy
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
            && $user->tokenCan(Abilities::ShowTransaction)
            && Auth::user()::class == Emploie::class
        ) {
            return true;
        }

        if ($visibility != 'all' && $user->tokenCan(Abilities::ShowOwnTransaction)) {
            return true;
        }
    }

    public function show(User $user, Transaction $transaction)
    {
        if (
            $user->tokenCan(Abilities::ShowOwnTransaction)
            && $transaction->from()->client_id == $user->id
            && $transaction->to()->client_id == $user->id
        ) {
            return true;
        }

        if ($user->tokenCan(Abilities::ShowTransaction)) {
            return true;
        }

        return false;
    }

    public function store(User $user, $client_id)
    {
        if ($user->tokenCan(Abilities::CreateOwnTransaction)) {
            return true;
        }

        if (
            $user->id == $client_id
            && $user::class == Client::class
            && $user->tokenCan(Abilities::CreateOwnTransaction)
        ) {
            return true;
        }

        return false;
    }
}
