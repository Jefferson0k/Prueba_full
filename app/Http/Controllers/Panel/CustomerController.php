<?php

namespace App\Http\Controllers\Panel;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Routing\Controller;

class CustomerController extends Controller{
    public function store(StoreCustomerRequest $request){
        Customer::create($request->validated());
        return response()->json(['message' => 'Cliente guardado con éxito.']);
    }
}