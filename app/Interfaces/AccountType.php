<?php

namespace App\Interfaces;

use App\Enum\ClientTiers;
use App\Enum\EmployeePositions;

interface AccountType
{
    public ClientTiers|EmployeePositions $accountType { get; }
}
