@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 font-weight-bold">Panel Principal - Resumen de Alertas</h2>
    </div>

    <!-- Métrica de Alertas (KPI Cards) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Riesgo Alto</h6>
                    <h3 class="card-title text-danger mb-0">14 Estudiantes</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Riesgo Medio</h6>
                    <h3 class="card-title text-warning mb-0">28 Estudiantes</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Intervenciones Activas</h6>
                    <h3 class="card-title text-success mb-0">42 Casos</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Principal de Alertas -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">Alertas Recientes por Atender</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Estudiante</th>
                            <th>Programa</th>
                            <th>Nivel de Riesgo</th>
                            <th>Motivo Alerta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>Ingeniería de Sistemas</td>
                            <td><span class="badge bg-danger">Alto</span></td>
                            <td>Inasistencias (>30%)</td>
                            <td><button class="btn btn-sm btn-outline-primary">Ver Ficha</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection