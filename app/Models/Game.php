<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'cover',
    ];

    /**
     * Un juego tiene muchas guías.
     */
    public function guides(): HasMany
    {
        return $this->hasMany(Guide::class);
    }
}