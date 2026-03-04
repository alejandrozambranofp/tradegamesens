<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    // Esto permite que Tinker y los formularios guarden datos
    protected $fillable = ['title', 'slug', 'cover']; 

    public function guides()
    {
        return $this->hasMany(Guide::class);
    }
}