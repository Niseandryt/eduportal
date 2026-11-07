<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Maestro extends Model
{
    /** @use HasFactory<\Database\Factories\MaestroFactory> */
    use HasFactory;

    protected $table = 'maestros';
    protected $fillable = ['user_id', 'departamento'];

    protected static function booted(): void
    {
        static::saving(function (Maestro $maestro) {
            if (Alumno::where('user_id', $maestro->user_id)->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'Este usuario ya esta registrado como alumno.',
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'maestro_id');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'maestro_id');
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'maestro_id');
    }
}
