<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

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




    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images/users')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (env('RESIZE_IMAGE') === true) {

            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 300))
                ->height(env('IMAGE_HEIGHT', 300));
        }
    }

    protected $appends = ['avatar_url']; // Esto hace que 'avatar_url' aparezca en el JSON enviado a Vue

    public function getAvatarUrlAttribute()
    {
        // 1. Intentar obtener desde Spatie Media Library
        $media = $this->getFirstMediaUrl('avatars');
        if ($media) {
            return $media;
        }

        // 2. Fallback al campo avatar (por compatibilidad o rutas directas)
        if ($this->avatar) {
            return asset($this->avatar);
        }

        // 3. Imagen por defecto
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
