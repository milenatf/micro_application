<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'expertise',
        'experiense'
    ];

    public function Student(): HasMany
    {
        return $this->hasMany(Student::class); // FK in students table: teacher_id
    }
}
