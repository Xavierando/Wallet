<?php

namespace App\Http\Filters\V1;

class TransactionFilter extends QueryFilter
{
    protected $sortable = [
        'to',
        'amount',
        'from',
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

    public function to($value)
    {
        return $this->builder->where('to', $value);
    }

    public function from($value)
    {
        return $this->builder->where('from', $value);
    }

    public function amount($value)
    {
        $amounts = array_map(fn ($v) => $v * 100, explode(',', $value));

        if (count($amounts) > 1) {
            return $this->builder->whereBetween('amount', $amounts);
        }

        return $this->builder->whereDate('amount', $amounts[0]);
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
