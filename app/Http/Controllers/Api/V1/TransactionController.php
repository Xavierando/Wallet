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
     * @response {"data":[{"id":1,"attributes":{"from":1,"to":2,"amount":10}]},"relationships":{"fromwallet":{"data":{"type":wallet,"id":1}},"towallet":{"data":{"type":wallet,"id":2}}}}}
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
     * @response {"data":{"id":1,"attributes":{"from":1,"to":2,"amount":10}},"relationships":{"fromwallet":{"data":{"type":wallet,"id":1}},"towallet":{"data":{"type":wallet,"id":2}}}}}
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
     * @response {"data":{"id":1,"attributes":{"from":1,"to":2,"amount":10},"relationships":{"fromwallet":{"data":{"type":wallet,"id":1}},"towallet":{"data":{"type":wallet,"id":2}}}}}
     */
    public function show(Transaction $transaction)
    {
        if ($this->isAbleTo('show', [$transaction])) {
            return new TransactionResource($transaction);
        }

        return $this->notAuthorized('Unauthorized');
    }
}
