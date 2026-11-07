<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    /** @use HasFactory<\Database\Factories\GrupoFactory> */
    use HasFactory;
    protected $table = 'grupos';
    protected $fillable = ['nombre', 'descripcion', 'maestro_id'];

    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'maestro_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'inscripciones')->withTimestamps();
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function foros(): HasMany
    {
        return $this->hasMany(Foro::class);
    }
}
