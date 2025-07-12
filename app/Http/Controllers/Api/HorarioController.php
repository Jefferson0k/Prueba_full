<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Horario\HorarioStoreRequest;
use App\Http\Requests\Horario\HorarioUpdateRequest;
use App\Http\Resources\Horario\HorarioResource;
use App\Models\Horario;

class HorarioController extends Controller{
    public function index(){
        $query = Horario::query();
        if (request('search')) {
            $query->whereRaw("CAST(nombre AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
        }
        return HorarioResource::collection($query->paginate(10));
    }
    public function show(Horario $horario){
        return new HorarioResource($horario);
    }
    public function store(HorarioStoreRequest $request){
        Horario::create($request->validated());
        return response()->json(['message' => 'Horario guardado con éxito.']);
    }
    public function update(HorarioUpdateRequest $request, Horario $horario){
        $horario->update($request->validated());
        return response()->json(['message' => 'Horario actualizado con éxito.']);
    }
    public function destroy(Horario $horario){
        $horario->delete();
        return response()->json(['message' => 'Horario eliminado con éxito.']);
    }
}
