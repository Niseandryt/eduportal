<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Entrega extends Pivot
{
    /** @use HasFactory<\Database\Factories\EntregaFactory> */
    use HasFactory;
    protected $table = 'entregas';
    public $incrementing = true;
    protected $fillable = ['tarea_id', 'alumno_id', 'contenido', 'calificacion'];

    protected function casts(): array
    {
        return [
            'calificacion' => 'decimal:2',
        ];
    }

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}