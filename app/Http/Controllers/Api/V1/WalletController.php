<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\WalletFilter;
use App\Http\Requests\Api\V1\WalletStoreRequest;
use App\Http\Resources\Api\V1\WalletResource;
use App\Models\Client;
use App\Models\Wallet;
use App\Policies\Api\V1\WalletPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends ApiController
{
    protected $policyClass = WalletPolicy::class;

    /**
     * Get all Wallets.
     *
     * @group Wallets
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[amount] Filter by amount. for a range filter input 2 value in crescent order separated by comma. Exemple: 10,1000
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *myWallet*
     * @queryParam filter[UpdateAt] Filter by last update. for a range filter, input 2 value in crescent order separated by comma.
     */
    public function index(Request $request, WalletFilter $filter)
    {

        if ($this->isAbleTo('index', ['all'])) {
            return WalletResource::collection(Wallet::filter($filter)->paginate());
        }

        if ($this->isAbleTo('index', []) && Auth::user()::class == Client::class) {
            $request->merge(['filter.client' => Auth::user()->id]);

            return WalletResource::collection(Wallet::filter($filter)->paginate());
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Store a newly created wallet in storage.
     *
     * @authenticated
     *
     * @group Wallets
     *
     * @request {"data":{"attributes":{"title":"new wallet"}}}
     *
     * @response {"data":{"id":1,"attributes":{"title":"new wallet","amount":0},"relationships":{"client":{"data":{"id":1}}}}}
     */
    public function store(WalletStoreRequest $request)
    {
        if (! isset($request->mappedAttributes()['client_id'])) {
            $request->merge(['data.relationships.client.data.id' => Auth::user()->id]);
        }

        if ($this->isAbleTo('store', [$request->mappedAttributes()['client_id']])) {
            return new WalletResource(Wallet::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Display the specified resource.
     *
     * @authenticated
     *
     * @group Wallets
     *
     * @response {"data":{"id":1,"attributes":{"title":"new wallet","amount":0},"relationships":{"client":{"data":{"id":1}}}}}
     */
    public function show(Wallet $wallet)
    {
        if ($this->isAbleTo('show', [$wallet])) {
            return new WalletResource($wallet);
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Update the specified resource in storage.
     *
     * @authenticated
     *
     * @group Wallets
     *
     * @request {"data":{"attributes":{"title":"new wallet"}}}
     */
    public function update(WalletStoreRequest $request, Wallet $wallet)
    {
        if ($this->isAbleTo('update', [$wallet])) {
            $wallet->update($request->mappedAttributes());

            return new WalletResource($wallet);
        }

        return $this->notAuthorized('Unauthorized');
    }

    /**
     * Remove the specified resource from storage. Deleted Wallet must have a zero amount.
     *
     * @authenticated
     *
     * @group Wallets
     */
    public function destroy(Wallet $wallet)
    {
        if ($this->isAbleTo('destroy', [$wallet])) {
            if ($wallet->amount == 0) {
                $wallet->delete();

                return $this->ok('wallet deleted');
            } else {
                return $this->notAuthorized('Can\'t delete a wallet with a non-zero amount');
            }
        }

        return $this->notAuthorized('Unauthorized');
    }
}
