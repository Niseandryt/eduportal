<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
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

    public function scopeVisiblePara(Builder $query, User $user): void
    {
        $query->where(function (Builder $query) use ($user) {
            $query->whereHas('users', fn (Builder $query) => $query->where('users.id', $user->id))
                ->orWhereHas('maestro', fn (Builder $query) => $query->where('user_id', $user->id));
        });
    }

    public function perteneceUsuario(User $user): bool
    {
        return static::visiblePara($user)->whereKey($this->id)->exists();
    }

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
