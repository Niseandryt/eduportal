<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialFactory> */
    use HasFactory;
    protected $table = 'materiales';
    protected $fillable = ['grupo_id', 'maestro_id', 'titulo', 'descripcion', 'enlace'];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function maestro(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'maestro_id');
    }
}