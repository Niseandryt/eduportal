<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    /** @use HasFactory<\Database\Factories\TareaFactory> */
    use HasFactory;
    protected $table = 'tareas';
    protected $fillable = ['grupo_id', 'maestro_id', 'titulo', 'descripcion', 'fecha_entrega'];
}