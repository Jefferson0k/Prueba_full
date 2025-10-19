<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\RateType\RateTypeResource;
use App\Models\RateType;

class RateTypeController extends Controller{
    public function index(){
        $rateTypes = RateType::all();
        return RateTypeResource::collection($rateTypes);
    }
}
