<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAT - Sistema de Retención Estudiantil</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="d-flex" id="wrapper">
        <!-- Sidebar Navigation -->
        <div class="bg-dark text-white p-3 min-vh-100" style="width: 250px;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="bi bi-shield-check fs-4 me-2 text-danger"></i>
                <span class="fs-5 fw-bold">SAAT Retención</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i> Centro de Alertas
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-person-vcard me-2"></i> Ficha Estudiante 360°
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-journal-plus me-2"></i> Intervenciones
                    </a>
                </li>
            </ul>
        </div>

        <div class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
                <div class="container-fluid p-0">
                    <span class="navbar-brand mb-0 h1 fs-6">Sistema de Acompañamiento y Alertas Tempranas</span>
                    <div class="d-flex align-items-center">
                        <span class="me-3 small text-muted">Prof. Docente</span>
                        <i class="bi bi-person-circle fs-4"></i>
                    </div>
                </div>
            </nav>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>