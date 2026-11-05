<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Entrega extends Pivot
{
    /** @use HasFactory<\Database\Factories\EntregaFactory> */
    use HasFactory;
    protected $table = 'entregas';
    public $incrementing = true;
    protected $fillable = ['tarea_id', 'alumno_id', 'contenido', 'calificacion'];
}