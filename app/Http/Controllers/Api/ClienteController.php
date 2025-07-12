<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\ClienteStoreRequest;
use App\Http\Requests\Cliente\ClienteUpdateRequest;
use App\Http\Resources\Cliente\ClienteResource;
use App\Models\Cliente;

class ClienteController extends Controller{
    public function index(){
        $query = Cliente::query();
        if (request('search')) {
            $query->whereRaw("CAST(nombres AS TEXT) ILIKE ?", ['%' . request('search') . '%'])
                  ->orWhereRaw("CAST(apellidos AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
        }
        return ClienteResource::collection($query->paginate(10));
    }
    public function show(Cliente $cliente){
        return new ClienteResource($cliente);
    }
    public function store(ClienteStoreRequest $request){
        Cliente::create($request->validated());
        return response()->json(['message' => 'Cliente guardado con éxito.']);
    }
    public function update(ClienteUpdateRequest $request, Cliente $cliente){
        $cliente->update($request->validated());
        return response()->json(['message' => 'Cliente actualizado con éxito.']);
    }
    public function destroy(Cliente $cliente){
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado con éxito.']);
    }
}
