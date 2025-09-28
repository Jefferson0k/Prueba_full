<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Traits\GeneratesCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model {
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, GeneratesCode;
    protected $table = 'product_categories';
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function products() {
        return $this->hasMany(Product::class, 'category_id');
    }
    protected static function boot() {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = $category->generateUniqueCode('PC');
            }
        });
    }
}
