<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar | Módulo Administrativo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --danger-color: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e1e2d 0%, #312e81 100%);
            color: var(--text-main);
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border-radius: 18px;
            padding: 34px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
        }

        .brand {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .brand span {
            color: var(--primary-color);
        }

        .subtitle {
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 13px 14px;
            font-size: 1rem;
            outline: none;
        }

        input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 18px;
            font-weight: 600;
        }

        button {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 14px;
            background: var(--primary-color);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        button:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand"><span>Admin</span>Panel</div>
        <p class="subtitle">Ingrese su usuario para acceder al panel administrativo.</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input id="usuario" name="usuario" type="text" value="{{ old('usuario') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required>
            </div>

            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
