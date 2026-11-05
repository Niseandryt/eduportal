<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    /** @use HasFactory<\Database\Factories\RespuestaFactory> */
    use HasFactory;
    protected $table = 'respuestas';
    protected $fillable = ['foro_id', 'user_id', 'contenido'];
}