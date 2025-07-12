<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Piso\PisoStoreRequest;
use App\Http\Requests\Piso\PisoUpdateRequest;
use App\Http\Resources\Piso\PisoResource;
use App\Models\Piso;

class PisoController extends Controller{
    public function index(){
        $query = Piso::query();
        if (request('search')) {
            $query->whereRaw("CAST(numero AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
        }
        return PisoResource::collection($query->paginate(10));
    }
    public function show(Piso $piso){
        return new PisoResource($piso);
    }
    public function store(PisoStoreRequest $request){
        Piso::create($request->validated());
        return response()->json(['message' => 'Piso guardado con éxito.']);
    }
    public function update(PisoUpdateRequest $request, Piso $piso){
        $piso->update($request->validated());
        return response()->json(['message' => 'Piso actualizado con éxito.']);
    }
    public function destroy(Piso $piso){
        $piso->delete();
        return response()->json(['message' => 'Piso eliminado con éxito.']);
    }
}
