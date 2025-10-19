<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagoPersonal\StorePagoPersonalRequest;
use App\Http\Resources\PagoPersonal\PagoPersonalResource;
use App\Models\PagoPersonal;
use App\Pipelines\PagosPersonal\PorEstado;
use App\Pipelines\PagosPersonal\PorPeriodo;
use App\Pipelines\PagosPersonal\PorSucursal;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PagoPersonalController extends Controller{
    public function index(){
        $pagos = app(Pipeline::class)
            ->send(PagoPersonal::query()->with(['empleado', 'sucursal', 'registradoPor']))
            ->through([
                PorSucursal::class,
                PorEstado::class,
                PorPeriodo::class,
            ])
            ->thenReturn()
            ->latest('fecha_pago')
            ->paginate(15);
        return PagoPersonalResource::collection($pagos);
    }
    public function store(StorePagoPersonalRequest $request){
        try {
            DB::beginTransaction();
            $userAuth = Auth::user();
            $data = [
                'user_id' => $request->user_id,
                'sub_branch_id' => $userAuth->sub_branch_id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'periodo' => $request->periodo,
                'tipo_pago' => $request->tipo_pago,
                'metodo_pago' => $request->metodo_pago,
                'concepto' => $request->concepto,
                'estado' => $request->estado,
                'registrado_por' => $userAuth->id,
            ];
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('comprobantes', $fileName, 'public');
                $data['comprobante'] = $path;
            }
            $pago = PagoPersonal::create($data);
            DB::commit();
            return response()->json([
                'message' => 'Pago registrado correctamente.',
                'data' => new PagoPersonalResource($pago)
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return response()->json([
                'message' => 'Error al registrar el pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id){}
    public function update($id){}
    public function delete($id){}
    public function hitorial(){}
}