<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    use ApiResponses;

    protected $policyClass;

    /**
     * check authorization against the Controller defined policy
     */
    protected function isAbleTo(string $ability, $options)
    {
        try {
            $policy = new $this->policyClass;

            if (! method_exists($policy, $ability)) {
                throw new Exception('');
            }

            return $policy->$ability(Auth::user(), ...$options);
        } catch (Exception $ex) {
            return false;
        }
    }
}
