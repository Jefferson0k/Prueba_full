<?php
namespace App\Http\Requests\PagoPersonal;

use App\Models\PagoPersonal;
use Illuminate\Foundation\Http\FormRequest;

class StorePagoPersonalRequest extends FormRequest{
    public function authorize(): bool{
        return $this->user()->can('create', PagoPersonal::class);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'periodo' => 'required|string|max:20',
            'tipo_pago' => 'required|in:salario,adelanto,bonificacion,comision,otro',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'concepto' => 'nullable|string|max:255',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'estado' => 'required|in:pendiente,pagado,anulado',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Debe seleccionar un empleado.',
            'user_id.exists' => 'El empleado seleccionado no existe.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'fecha_pago.required' => 'Debe indicar la fecha de pago.',
            'periodo.required' => 'Debe indicar el periodo correspondiente.',
            'tipo_pago.required' => 'Debe especificar el tipo de pago.',
            'tipo_pago.in' => 'El tipo de pago no es válido.',
            'metodo_pago.required' => 'Debe especificar el método de pago.',
            'metodo_pago.in' => 'El método de pago no es válido.',
            'estado.in' => 'El estado debe ser pendiente, pagado o anulado.',
            'comprobante.file' => 'El comprobante debe ser un archivo válido.',
            'comprobante.mimes' => 'El comprobante debe ser un archivo JPG, PNG o PDF.',
            'comprobante.max' => 'El comprobante no debe superar los 5MB.',
        ];
    }
}