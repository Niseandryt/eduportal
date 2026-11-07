<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Inscripcion extends Pivot
{
    /** @use HasFactory<\Database\Factories\InscripcionFactory> */
    use HasFactory;
    protected $table = 'inscripciones';
    public $incrementing = true;
    protected $fillable = ['grupo_id', 'user_id'];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
