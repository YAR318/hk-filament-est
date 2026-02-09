<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - HK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 48px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 48px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            box-shadow: 0 10px 30px -5px rgba(233, 69, 96, 0.4);
        }

        .user-name {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .user-email {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 24px;
        }

        .role-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, rgba(233, 69, 96, 0.2) 0%, rgba(255, 107, 107, 0.2) 100%);
            border: 1px solid rgba(233, 69, 96, 0.3);
            border-radius: 50px;
            color: #ff6b6b;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 32px;
        }

        .info-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .info-title {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .info-value {
            color: #fff;
            font-weight: 500;
            font-size: 14px;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 10px 30px -5px rgba(233, 69, 96, 0.4);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(233, 69, 96, 0.5);
        }

        .logout-btn svg {
            width: 20px;
            height: 20px;
        }

        .welcome-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="profile-card">
        <div class="avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <p class="welcome-text">Bienvenido de vuelta</p>
        <h1 class="user-name">{{ $user->name }}</h1>
        <p class="user-email">{{ $user->email }}</p>

        <span class="role-badge">
            {{ $user->role === 'user' ? 'Usuario' : ucfirst($user->role) }}
        </span>

        <div class="info-section">
            <p class="info-title">Información de la cuenta</p>

            <div class="info-item">
                <span class="info-label">Estado</span>
                <span class="info-value" style="color: #4ade80;">● Activo</span>
            </div>

            <div class="info-item">
                <span class="info-label">Miembro desde</span>
                <span class="info-value">{{ $user->created_at->format('d M, Y') }}</span>
            </div>

            @if($user->email_verified_at)
            <div class="info-item">
                <span class="info-label">Email verificado</span>
                <span class="info-value" style="color: #4ade80;">✓ Verificado</span>
            </div>
            @endif
        </div>

        <div class="info-section" style="margin-top: -16px; margin-bottom: 24px; padding: 16px;">
            <a href="{{ env('AUTH_SERVER_URL', 'http://localhost:8001') }}/profile"
                style="display: flex; align-items: center; justify-content: center; gap: 10px; color: #fff; text-decoration: none; font-size: 14px; font-weight: 500;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Editar Perfil y Contraseña
            </a>
        </div>

        <form action="{{ route('profile.logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</body>

</html>