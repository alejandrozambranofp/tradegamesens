<?php

namespace App\Models;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    // 1. Campos que permitimos rellenar desde el formulario
    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'status',
        'game_id',
        'image'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset($this->image);
        }
        return null;
    }

    // 2. Relación: Una guía pertenece a un Usuario (Autor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Relación: Una guía pertenece a un Juego
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // 4. Relación: Una guía tiene muchas Categorías (N:M)
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_guide');
    }

    // 5. Relación: Una guía tiene muchas Valoraciones (1:N)
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_guide_user');
    }
}
