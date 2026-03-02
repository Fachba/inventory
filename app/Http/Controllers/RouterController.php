<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouterController extends Controller
{
    public $session;

    public function __construct()
    {
        date_default_timezone_set("Asia/Jakarta");

        $this->middleware(function ($request, $next) {
            // var_dump($request->auth);
            return $next($request);
        });
    }
}
