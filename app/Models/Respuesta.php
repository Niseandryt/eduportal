<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Respuesta extends Model
{
    /** @use HasFactory<\Database\Factories\RespuestaFactory> */
    use HasFactory;
    protected $table = 'respuestas';
    protected $fillable = ['foro_id', 'user_id', 'contenido'];

    public function foro(): BelongsTo
    {
        return $this->belongsTo(Foro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}