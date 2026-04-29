<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'surname1',
        'surname2',
        'avatar',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    protected $appends = ['avatar_url']; // Esto hace que 'avatar_url' aparezca en el JSON enviado a Vue

    public function getAvatarUrlAttribute()
    {
        // Usar el campo avatar (rutas directas en storage/app/public/avatars)
        if ($this->avatar) {
            // Asegurarse de que la ruta sea correcta para asset()
            // Si ya empieza con /storage/, asset() lo manejará bien.
            return asset($this->avatar);
        }

        // Imagen por defecto
        return asset('images/placeholder-avatar.jpg');
    }

    public function favorites()
    {
        return $this->belongsToMany(Guide::class, 'favorite_guide_user');
    }

    public function guides()
    {
        return $this->hasMany(Guide::class);
    }

    // Relación N:M explícita para la tabla pivot 'ratings' con campos extra (Para rúbrica M0613 RA6)
    public function ratedGuides()
    {
        return $this->belongsToMany(Guide::class, 'ratings')
                    ->withPivot('score', 'comment')
                    ->withTimestamps();
    }
}
