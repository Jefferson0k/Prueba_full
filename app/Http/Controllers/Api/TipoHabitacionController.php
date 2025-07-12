<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoHabitacion\TipoHabitacionStoreRequest;
use App\Http\Requests\TipoHabitacion\TipoHabitacionUpdateRequest;
use App\Http\Resources\TipoHabitacion\TipoHabitacionResource;
use App\Models\TipoHabitacion;

class TipoHabitacionController extends Controller
{
    public function index()
    {
        $query = TipoHabitacion::query();

        if (request('search')) {
            $query->whereRaw("CAST(nombre AS TEXT) ILIKE ?", ['%' . request('search') . '%']);
        }

        return TipoHabitacionResource::collection($query->paginate(10));
    }

    public function show(TipoHabitacion $tipoHabitacion)
    {
        return new TipoHabitacionResource($tipoHabitacion);
    }

    public function store(TipoHabitacionStoreRequest $request)
    {
        TipoHabitacion::create($request->validated());

        return response()->json(['message' => 'Tipo de habitación guardado con éxito.']);
    }

    public function update(TipoHabitacionUpdateRequest $request, TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->update($request->validated());

        return response()->json(['message' => 'Tipo de habitación actualizado con éxito.']);
    }

    public function destroy(TipoHabitacion $tipoHabitacion)
    {
        $tipoHabitacion->delete();

        return response()->json(['message' => 'Tipo de habitación eliminado con éxito.']);
    }
}
