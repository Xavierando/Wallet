<?php

namespace App\Http\Filters\V1;

class WalletFilter extends QueryFilter
{
    protected $sortable = [
        'title',
        'amount',
        'client' => 'client_id',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    public function createdAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }

    public function include($value)
    {
        return $this->builder->with($value);
    }

    public function client($value)
    {
        return $this->builder->where('client_id', $value);
    }

    public function amount($value)
    {
        $amounts = array_map(fn ($v) => $v * 100, explode(',', $value));

        if (count($amounts) > 1) {
            return $this->builder->whereBetween('amount', $amounts);
        }

        return $this->builder->where('amount', $amounts[0]);
    }

    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeStr);
    }

    public function updatedAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }
}
