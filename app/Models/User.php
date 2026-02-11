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

    public bool $is_syncing_from_operator = false;

    public function canAccessPanel(Panel $panel): bool
    {
        // Permitimos el login a todos, LoginResponse redirigirá según rol
        return $this->can('acceder_panel');
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            // Asignar rol de Spatie si no es 'user'
            if ($user->role !== 'user') {
                $user->syncRoles($user->role);
            }

            // Sincronizar con tabla operators solo si NO viene de una sincronización desde Operator
            if (!$user->is_syncing_from_operator && $user->role === 'operador') {
                $user->syncOperator();
            }
        });

        static::updated(function (User $user) {
            if ($user->isDirty('role')) {
                if ($user->role === 'user') {
                    $user->roles()->detach();
                    // Eliminar de operators si existía
                    if (!$user->is_syncing_from_operator) {
                        Operator::where('email', $user->email)->delete();
                    }
                }
                else {
                    $user->syncRoles($user->role);

                    // Sincronizar con tabla operators
                    if (!$user->is_syncing_from_operator) {
                        if ($user->role === 'operador') {
                            $user->syncOperator();
                        }
                        else {
                            // Si cambió a admin o supervisor, eliminar de operators (o actualizar si decidimos mantener sync para todos)
                            // Por ahora mantenemos la lógica original: solo operadores van a operators?
                            // ESPERA: El requerimiento es que Admin/Supervisor TAMBIÉN tengan usuario.
                            // PERO: La tabla `operators` parece ser solo para gente que atiende chats.
                            // Si un Admin crea un Operator "Admin", debería tener usuario Admin.
                            // Si un User Admin se crea, ¿debe tener Operator?
                            // Asumiremos que SI, si el usuario quiere que se vinculen.
                            // Pero el código original borraba el operator si no era 'operador'.
                            // MODIFICACIÓN: Si el rol es admin/supervisor Y existe en operators, actualizarlo.
                            // Si no existe, no forzar la creación (solo Operator -> User es forzoso).

                            // Para simplificar y cumplir el deseo del usuario "vincular":
                            // Si el User se actualiza, buscamos si hay un Operator con ese email y lo actualizamos.
                            $operator = Operator::where('email', $user->email)->first();
                            if ($operator) {
                                $operator->update([
                                    'role' => $user->role,
                                    'name' => $user->name,
                                ]);
                            }
                            elseif ($user->role === 'operador') {
                                $user->syncOperator();
                            }
                        }
                    }
                }
            }
            else {
                // Si cambió nombre o email, actualizar en operators
                if (!$user->is_syncing_from_operator && ($user->isDirty('name') || $user->isDirty('email'))) {
                    $operator = Operator::where('email', $user->getOriginal('email'))->first(); // Usar email original para encontrarlo
                    if ($operator) {
                        $operator->update([
                            'email' => $user->email,
                            'name' => $user->name,
                        ]);
                    }
                    elseif ($user->role === 'operador') {
                        $user->syncOperator();
                    }
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