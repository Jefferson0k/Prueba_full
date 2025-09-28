<?php

namespace App\Traits;

trait GeneratesCode
{
    public function generateUniqueCode($prefix = '', $length = 8)
    {
        $table = $this->getTable();
        $column = 'code';
        
        if (property_exists($this, 'codeColumn')) {
            $column = $this->codeColumn;
        }

        do {
            $code = $prefix . strtoupper(substr(uniqid(), -$length));
        } while (static::where($column, $code)->exists());

        return $code;
    }
}
