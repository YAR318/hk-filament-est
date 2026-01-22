# Crear Usuario Admin para Filament

Para crear tu primer usuario administrador en Filament, ejecuta el siguiente comando en PowerShell:

```powershell
cd HK_Filament_EST
php artisan make:filament-user
```

Te pedirá:
- **Name**: Tu nombre (ej: Admin)
- **Email**: Tu email (ej: admin@admin.com)
- **Password**: Tu contraseña (mínimo 8 caracteres)

Luego podrás acceder al panel en:
**http://hk_filament_est.test/admin**
