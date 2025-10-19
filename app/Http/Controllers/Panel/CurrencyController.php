<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency;

class CurrencyController extends Controller{
    public function index(){
        $currencies = Currency::all();
        return CurrencyResource::collection($currencies);
    }
}
