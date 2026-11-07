<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Alumno extends Model
{
    /** @use HasFactory<\Database\Factories\AlumnoFactory> */
    use HasFactory;

    protected $table = 'alumnos';
    protected $fillable = ['user_id', 'carnet'];

    protected static function booted(): void
    {
        static::saving(function (Alumno $alumno) {
            if (Maestro::where('user_id', $alumno->user_id)->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'Este usuario ya esta registrado como maestro.',
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'alumno_id');
    }
}
