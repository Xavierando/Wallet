<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TranfertFactory> */
    use HasFactory;

    public function to() : BelongsTo
    {
        return $this->belongsTo(Wallet::class,'to');
    }

    public function from() : HasOne
    {
        return $this->hasOne(Wallet::class,'from');
    }
}
