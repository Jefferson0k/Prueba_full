<?php
namespace App\Http\Resources\PagoPersonal;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PagoPersonalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'empleado' => $this->empleado?->name . ' ' . $this->empleado?->apellidos,
            'empleado_id' => $this->empleado?->id,
            'sucursal' => $this->sucursal?->name,
            'sucursal_id' => $this->sucursal?->id,
            'monto' => $this->monto,
            'monto_formateado' => $this->monto_formateado,
            'fecha_pago' => $this->fecha_pago->format('Y-m-d'),
            'fecha_pago_formateada' => $this->fecha_pago->format('d/m/Y'),
            'periodo' => $this->periodo,
            'tipo_pago' => $this->tipo_pago,
            'metodo_pago' => $this->metodo_pago,
            'concepto' => $this->concepto,
            'estado' => $this->estado,
            
            // Información del comprobante
            'comprobante' => $this->comprobante,
            'comprobante_url' => $this->comprobante 
                ? Storage::disk('public')->url($this->comprobante)
                : null,
            'tiene_comprobante' => !is_null($this->comprobante),
            'tipo_comprobante' => $this->comprobante 
                ? $this->getTipoComprobante($this->comprobante)
                : null,
            
            'registrado_por' => $this->registradoPor?->name,
            'registrado_por_id' => $this->registradoPor?->id,
            'creado_en' => $this->created_at->format('d/m/Y H:i:s'),
            'actualizado_en' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Determina el tipo de archivo del comprobante
     */
    private function getTipoComprobante($comprobante)
    {
        $extension = pathinfo($comprobante, PATHINFO_EXTENSION);
        
        $tipos = [
            'pdf' => 'pdf',
            'jpg' => 'imagen',
            'jpeg' => 'imagen',
            'png' => 'imagen',
        ];

        return $tipos[strtolower($extension)] ?? 'otro';
    }
}