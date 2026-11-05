<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foro extends Model
{
    /** @use HasFactory<\Database\Factories\ForoFactory> */
    use HasFactory;
    protected $table = 'foros';
    protected $fillable = ['grupo_id', 'user_id', 'titulo', 'contenido'];
}