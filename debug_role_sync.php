<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Buscar un usuario Admin para probar (que no sea el principal si es posible, o creamos uno dummy)
$user = User::where('email', 'yeremigamer555@gmail.com')->first();

if (!$user) {
    echo "No encontrado el usuario yeremigamer555@gmail.com, buscando otro...\n";
    $user = User::where('role', 'admin')->first();
}

if (!$user) {
    die("No hay usuarios admin para probar.\n");
}

echo "Usuario encontrado: {$user->name} ({$user->email})\n";
echo "Rol actual (atributo): {$user->role}\n";
echo "Roles actuales (Spatie): " . $user->getRoleNames()->implode(', ') . "\n";

// 2. Intentar cambiar a 'operador'
echo "\n--- Intentando cambiar a 'operador' ---\n";
$user->role = 'operador';
$user->save(); // Esto debería disparar el evento updated

echo "Rol nuevo (atributo): {$user->role}\n";
echo "Roles nuevos (Spatie): " . $user->getRoleNames()->implode(', ') . "\n";

// 3. Verificar si funcionó
if ($user->hasRole('operador') && !$user->hasRole('admin')) {
    echo "\n✅ ÉXITO: El usuario ahora es operador y ya no es admin.\n";
} else {
    echo "\n❌ FALLO: Los roles de Spatie no se sincronizaron correctamente.\n";
    if ($user->hasRole('admin'))
        echo "  - Sigue teniendo rol admin.\n";
    if (!$user->hasRole('operador'))
        echo "  - No tiene rol operador.\n";
}

// 4. Revertir cambios (opcional, para no romperle el usuario)
// echo "\n--- Revertiendo cambios ---\n";
// $user->role = 'admin';
// $user->save();
// echo "Rol revertido a: " . $user->getRoleNames()->implode(', ') . "\n";
