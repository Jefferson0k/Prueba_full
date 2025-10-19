<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CashRegister extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, \OwenIt\Auditing\Auditable;

    // ─── Campos Asignables ─────────────────────────────────────────────────────
    protected $fillable = [
        'sub_branch_id',
        'name',
        'status',
        'opened_by',
        'closed_by',
        'opening_amount',
        'closing_amount',
        'opened_at',
        'closed_at',
        'is_active',
    ];

    // ─── Casts ─────────────────────────────────────────────────────────────────
    protected $casts = [
        'opened_at'  => 'datetime',
        'closed_at'  => 'datetime',
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ─── Relaciones ────────────────────────────────────────────────────────────
    public function subBranch()
    {
        return $this->belongsTo(SubBranch::class, 'sub_branch_id');
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpened($query)
    {
        return $query->where('status', 'abierta');
    }

    // ─── Métodos de Estado ─────────────────────────────────────────────────────
    public function isOpen(): bool
    {
        return $this->status === 'abierta' && $this->is_active;
    }

    public function isClosed(): bool
    {
        return $this->status === 'cerrada';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'bloqueada';
    }
    public static function createMultipleCashRegisters(int $quantity, ?int $subBranchId = null)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                throw new \Exception('Usuario no autenticado');
            }

            $targetSubBranchId = $subBranchId ?? $user->sub_branch_id;

            if (!$targetSubBranchId) {
                throw new \Exception('No se pudo determinar la sub-sucursal');
            }

            $created = collect();

            for ($i = 1; $i <= $quantity; $i++) {
                $cashRegister = self::create([
                    'sub_branch_id' => $targetSubBranchId,
                    'name'          => 'Caja ' . $i,
                    'status'        => 'cerrada',
                    'is_active'     => true,
                    'created_by'    => $user->id ?? null,
                    'updated_by'    => $user->id ?? null,
                ]);

                $created->push($cashRegister);
            }

            return $created;

        } catch (\Exception $e) {
            Log::error('Error creando cajas registradoras: ' . $e->getMessage());
            return false;
        }
    }
    public function openCashRegister(float $openingAmount){
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('Usuario no autenticado');
            }
            
            if ($this->isOpen()) {
                throw new \Exception('La caja ya está abierta.');
            }
            
            // Verificar si el usuario ya tiene una caja abierta
            $userOpenCash = self::where('opened_by', $user->id)
                ->where('status', 'abierta')
                ->where('is_active', true)
                ->where('id', '!=', $this->id)
                ->first();
                
            if ($userOpenCash) {
                throw new \Exception('Ya tienes una caja abierta. Debes cerrarla antes de aperturar otra.');
            }
            
            $this->update([
                'status'          => 'abierta',
                'opened_by'       => $user->id,
                'opening_amount'  => $openingAmount,
                'opened_at'       => now(),
                'updated_by'      => $user->id,
            ]);
            
            return [
                'success' => true,
                'message' => 'Caja aperturada correctamente.',
                'cash_register' => $this,
            ];
        } catch (\Exception $e) {
            Log::error('Error al aperturar caja: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public static function getUserOpenCashRegister($userId = null){
        $user = $userId ? User::find($userId) : Auth::user();
        if (!$user) {
            return null;
        }
        return self::where('opened_by', $user->id)
            ->where('status', 'abierta')
            ->where('is_active', true)
            ->first();
    }
    public static function hasUserOpenCashRegister($userId = null): bool{
        return (bool) self::getUserOpenCashRegister($userId);
    }// En App\Models\CashRegister
public function isOpened(): bool
{
    return $this->isOpen(); // Alias para mantener compatibilidad
}
}