<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsoHabitacion\UsoHabitacionStoreRequest;
use App\Http\Requests\UsoHabitacion\UsoHabitacionUpdateRequest;
use App\Http\Resources\UsoHabitacion\UsoHabitacionResource;
use App\Models\UsoHabitacion;

class UsoHabitacionController extends Controller{
    public function index(){
        $query = UsoHabitacion::with(['habitacion', 'cliente', 'horario']);
        if (request('search')) {
            $query->whereHas('cliente', function ($q) {
                $q->whereRaw("CAST(nombres AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
            });
        }
        return UsoHabitacionResource::collection($query->paginate(10));
    }
    public function show(UsoHabitacion $usoHabitacion){
        return new UsoHabitacionResource($usoHabitacion->load(['habitacion', 'cliente', 'horario']));
    }
    public function store(UsoHabitacionStoreRequest $request){
        UsoHabitacion::create($request->validated());
        return response()->json(['message' => 'Registro de uso guardado con éxito.']);
    }
    public function update(UsoHabitacionUpdateRequest $request, UsoHabitacion $usoHabitacion){
        $usoHabitacion->update($request->validated());
        return response()->json(['message' => 'Registro de uso actualizado con éxito.']);
    }
    public function destroy(UsoHabitacion $usoHabitacion){
        $usoHabitacion->delete();
        return response()->json(['message' => 'Registro de uso eliminado con éxito.']);
    }
}
