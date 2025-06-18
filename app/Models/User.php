<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
     /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

   /**
     * Check if disabled account can login
     *
     * @return bool
     *
     */
    public function canLogin(): bool
    {
        return $this->is_active || (! $this->is_active && $this->disabled_by === $this->id);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        return asset('back/img/avatar/avatar-1.png');
    }
    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     */
    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }

}
