<?php

namespace App\Interfaces;

use App\Enum\ClientTiers;
use App\Enum\EmploiePositions;

interface AccountType
{
    public ClientTiers|EmploiePositions $accountType { get; }
}
