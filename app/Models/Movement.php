<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasAuditFields;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Movement extends Model implements AuditableContract
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, Auditable;

    protected $fillable = [
        'code',
        'date',
        'provider_id',
        'sub_branch_id',
        'payment_type',
        'credit_date',
        'includes_igv',
        'voucher_type',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected $dates = ['created_at', 'updated_at', 'date', 'credit_date'];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function subBranch(): BelongsTo
    {
        return $this->belongsTo(SubBranch::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(MovementDetail::class);
    }
}
