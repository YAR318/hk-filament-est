<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Operator;
use App\Models\User;

echo "=== OPERATORS EN BD ===\n";
$operators = Operator::all();
echo "Total: " . $operators->count() . "\n\n";

foreach ($operators as $op) {
    echo "ID: {$op->id}\n";
    echo "Name: {$op->name}\n";
    echo "Email: {$op->email}\n";
    echo "Phone: {$op->phone_number}\n";
    echo "Role: {$op->role}\n";
    echo "Status: {$op->status}\n";
    echo "Active: " . ($op->is_active ? 'Sí' : 'No') . "\n";
    echo "---\n";
}

echo "\n=== USUARIOS CON ROL OPERADOR/SUPERVISOR ===\n";
$users = User::whereIn('role', ['operador', 'supervisor', 'admin'])->get();
echo "Total: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "---\n";
}
