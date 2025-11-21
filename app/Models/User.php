<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory,HasRoles,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'position',
        'phone',
        'address',
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
            'is_active' => 'boolean',
        ];
    }

    // Konfigurasi akses panel filament
    public function canAccessPanel(Panel $panel): bool
    {
        // cantoh : hanya user aktif yang bisa logn
        // nantinya bisa dibatasi hanya Role 'super_admin' atau 'admin' yang bisa masuk panel

        return $this->isActive();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    // Relasi : Satu guru punya banyak jadwal
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // Relasi : Satu guru punya banyak absen
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
