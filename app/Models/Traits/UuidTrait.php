<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    public static function booted()
    {
        static::creating(function($model) {
            // Gera automaticamente um uuid para um aluno
            $model->{$model->getKeyName()} = (String) Str::uuid();
        });
    }
}
