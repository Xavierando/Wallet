<?php

namespace App\Permissions\V1;

use App\Enum\ClientTiers;
use App\Enum\EmployeePositions;
use App\Models\Client;
use App\Models\Employee;

final class Abilities
{
    public const ShowOwnWallet = 'wallet:own:show';

    public const CreateOwnWallet = 'wallet:own:create';

    public const UpdateOwnWallet = 'wallet:own:update';

    public const DeleteOwnWallet = 'wallet:own:delete';

    public const ShowWallet = 'wallet:show';

    public const CreateWallet = 'wallet:create';

    public const UpdateWallet = 'wallet:update';

    public const DeleteWallet = 'wallet:delete';

    public const ShowOwnTransaction = 'transaction:own:show';

    public const ShowTransaction = 'transaction:show';

    public const CreateOwnTransaction = 'transaction:own:create';

    public const CreateTransaction = 'transaction:create';

    public static function getAbilities(Client|Employee $user)
    {
        if ($user->accountType == ClientTiers::Basic || $user->accountType == ClientTiers::Pro || $user->accountType == ClientTiers::Diamond) {
            return [
                self::ShowOwnWallet,
                self::CreateOwnWallet,
                self::UpdateOwnWallet,
                self::DeleteOwnWallet,
                self::ShowOwnTransaction,
                self::CreateOwnTransaction,
            ];
        }

        if ($user->accountType == EmployeePositions::Normal || $user->accountType == EmployeePositions::Supervisor) {
            return [
                self::ShowWallet,
                self::CreateWallet,
                self::UpdateWallet,
                self::DeleteWallet,
                self::ShowTransaction,
                self::CreateTransaction,
            ];
        }

        return [];
    }
}
