<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAAT - Sistema de Retención Estudiantil</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
            padding-bottom: 0.5rem;
        }
        .nav-tabs .nav-link.active {
            color: #212529;
            border-bottom: 2px solid #212529;
            font-weight: 600;
            background: transparent;
        }
        .wireframe-box {
            border: 2px dashed #ced4da;
            border-radius: 8px;
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Encabezado Superior y Navegación por Pestañas -->
    <header class="bg-white border-bottom shadow-sm mb-4">
        <div class="container-fluid px-4 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light rounded border p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-square text-secondary fs-6"></i>
                    </div>
                    <span class="fw-bold fs-5 text-dark">SAAT</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <span class="text-muted small">{{ auth()->user()->correo_inst }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Cerrar sesión</button>
                        </form>
                    @else
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('login') }}">Iniciar sesión</a>
                    @endauth
                </div>
            </div>

            <!-- Menú de Pestañas -->
            <ul class="nav nav-tabs border-0 gap-4 fs-6">
                <li class="nav-item">
                    <a class="nav-link active px-1" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-1" href="{{ auth()->check() ? route('crud.index') : route('login') }}">Gestión CRUD</a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Contenido Dinámico de la Vista -->
    <main class="container-fluid px-4 pb-5">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>