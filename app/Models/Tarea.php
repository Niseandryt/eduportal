<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    /** @use HasFactory<\Database\Factories\TareaFactory> */
    use HasFactory;
    protected $table = 'tareas';
    protected $fillable = ['grupo_id', 'maestro_id', 'titulo', 'descripcion', 'fecha_entrega'];

    protected function casts(): array
    {
        return [
            'fecha_entrega' => 'date:Y-m-d',
        ];
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'maestro_id');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'tarea_id');
    }
}