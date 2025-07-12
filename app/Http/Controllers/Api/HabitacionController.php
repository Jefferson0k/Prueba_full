<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Habitacion\HabitacionStoreRequest;
use App\Http\Requests\Habitacion\HabitacionUpdateRequest;
use App\Http\Resources\Habitacion\HabitacionResource;
use App\Models\Habitacion;

class HabitacionController extends Controller{
    public function index(){
        $query = Habitacion::with(['piso', 'tipo']);
        if (request('search')) {
            $query->whereRaw("CAST(numero AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
        }
        return HabitacionResource::collection($query->paginate(10));
    }
    public function show(Habitacion $habitacion){
        return new HabitacionResource($habitacion->load(['piso', 'tipo']));
    }
    public function store(HabitacionStoreRequest $request){
        Habitacion::create($request->validated());
        return response()->json(['message' => 'Habitación guardada con éxito.']);
    }
    public function update(HabitacionUpdateRequest $request, Habitacion $habitacion){
        $habitacion->update($request->validated());
        return response()->json(['message' => 'Habitación actualizada con éxito.']);
    }
    public function destroy(Habitacion $habitacion){
        $habitacion->delete();
        return response()->json(['message' => 'Habitación eliminada con éxito.']);
    }
}
