<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Default page for API.
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke()
    {
        return response()->json($this->response);
    }
}