<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public static bool $needAuth = false;
    public static bool $onlyGuests = false;

    public function __construct()
    {
        if (static::$needAuth) {
            $this->middleware('auth:sanctum');
        }

        if (static::$onlyGuests) {
            $this->middleware('guest');
        }
    }
}
