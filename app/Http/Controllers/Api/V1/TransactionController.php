<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TransactionFilter;
use App\Http\Resources\Api\V1\TransactionResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Policies\Api\V1\TransactionPolicy;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    protected $policyClass = TransactionPolicy::class;

    /**
     * Display a listing of the resource.
     *
     * @authenticated
     *
     * @group Transactions
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[amount] Filter by amount. for a range filter input 2 value in crescent order separated by comma. Exemple: 10,1000
     * @queryParam filter[to] Filter from starting point of the operation. Example: 1
     * @queryParam filter[from] Filter from end point of the operation. Example: 2
     *
     * @response {
    "data": [
        {
            "type": "transaction",
            "id": 1,
            "attributes": {
                "to": 39,
                "from": 47,
                "amount": 7.72,
                "createdAt": "2025-07-21T12:43:17.000000Z",
                "updatedAt": "2025-07-21T12:43:17.000000Z"
            },
            "relationships": {
                "fromwallet": {
                    "data": {
                        "type": "wallet",
                        "id": 47
                    }
                },
                "towallet": {
                    "data": {
                        "type": "wallet",
                        "id": 39
                    }
                }
            }
        }
    ],
    "links": {
        "first": "http://127.0.0.1/api/wallets/47/transactions?page=1",
        "last": "http://127.0.0.1/api/wallets/47/transactions?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1/api/wallets/47/transactions?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://127.0.0.1/api/wallets/47/transactions",
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
     */
    public function index(TransactionFilter $filter, Wallet $wallet)
    {

        if ($this->isAbleTo('index', [$wallet])) {
            return TransactionResource::collection(Transaction::where('from', $wallet->id)->orWhere('to', $wallet->id)->filter($filter)->paginate());
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @authenticated
     *
     * @group Transactions
     *
     * @request {"data":{"attributes":{"from":1,"to":2,"amount":10}}}
     *
     * @response {
    "data": {
        "type": "transaction",
        "id": 1,
        "attributes": {
            "to": 39,
            "from": 47,
            "amount": 7.72,
            "createdAt": "2025-07-21T12:43:17.000000Z",
            "updatedAt": "2025-07-21T12:43:17.000000Z"
        },
        "relationships": {
            "fromwallet": {
                "data": {
                    "type": "wallet",
                    "id": 47
                }
            },
            "towallet": {
                "data": {
                    "type": "wallet",
                    "id": 39
                }
            }
        }
    }
}
     */
    public function store(Request $request)
    {

        if ($this->isAbleTo('store', [$request->mappedAttributes()['from']])) {
            return new TransactionResource(Transaction::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Display the specified resource.
     *
     * @authenticated
     *
     * @group Transactions
     *
     * @response {
    "data": {
        "type": "transaction",
        "id": 1,
        "attributes": {
            "to": 39,
            "from": 47,
            "amount": 7.72,
            "createdAt": "2025-07-21T12:43:17.000000Z",
            "updatedAt": "2025-07-21T12:43:17.000000Z"
        },
        "relationships": {
            "fromwallet": {
                "data": {
                    "type": "wallet",
                    "id": 47
                }
            },
            "towallet": {
                "data": {
                    "type": "wallet",
                    "id": 39
                }
            }
        }
    }
}
     */
    public function show(Wallet $wallet, Transaction $transaction)
    {
        if ($this->isAbleTo('index', [$wallet])) {
            return new TransactionResource($transaction);
        }

        return $this->notAuthorized('Unauthorized');
    }
}
