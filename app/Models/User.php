<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        // Permitimos el login a todos, LoginResponse redirigirá según rol
        return true;
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role !== 'user') {
                $user->syncRoles($user->role);
            }

            // Sincronizar con tabla operators SOLO si es operador
            if ($user->role === 'operador') {
                $user->syncOperator();
            }
        });

        static::updated(function (User $user) {
            if ($user->isDirty('role')) {
                if ($user->role === 'user') {
                    $user->roles()->detach();
                    // Eliminar de operators si existía
                    Operator::where('email', $user->email)->delete();
                } else {
                    $user->syncRoles($user->role);

                    // Sincronizar con tabla operators SOLO si es operador
                    if ($user->role === 'operador') {
                        $user->syncOperator();
                    } else {
                        // Si cambió a admin o supervisor, eliminar de operators
                        Operator::where('email', $user->email)->delete();
                    }
                }
            } else {
                // Si cambió nombre o email, actualizar en operators solo si es operador
                if (($user->isDirty('name') || $user->isDirty('email')) && $user->role === 'operador') {
                    $user->syncOperator();
                }
            }
        });
    }

    /**
     * Sincronizar usuario con tabla de operadores
     */
    public function syncOperator(): void
    {
        Operator::updateOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'phone_number' => $this->phone_number ?? '+52' . substr(str_replace(['@', '.', 'gmail', 'com'], '', $this->email), 0, 10),
                'role' => 'operador',
                'is_active' => true,
                'status' => 'offline',
                'max_concurrent_chats' => 5,
                'current_chats_count' => 0,
            ]
        );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
}
