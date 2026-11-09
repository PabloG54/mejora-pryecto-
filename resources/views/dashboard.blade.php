@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold fs-4 mb-1 text-dark">SAAT</h2>
            <p class="text-secondary mb-0">Sistema de alerta y acompañamiento estudiantil.</p>
        </div>
        @auth
            <a class="btn btn-primary" href="{{ route('crud.index') }}">Abrir gestión CRUD</a>
        @else
            <a class="btn btn-primary" href="{{ route('login') }}">Iniciar sesión para probar</a>
        @endauth
    </div>

    <!-- Fila de 4 Tarjetas Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">ESTUDIANTES</h6>
                    <a class="btn btn-outline-primary" href="{{ auth()->check() ? route('crud.index', ['resource' => 'students']) : route('login') }}">Abrir estudiantes</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">ALERTAS ACTIVAS</h6>
                    <a class="btn btn-outline-primary" href="{{ auth()->check() ? route('crud.index', ['resource' => 'early-warnings']) : route('login') }}">Ver alertas</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">RIESGO ALTO</h6>
                    <a class="btn btn-outline-primary" href="{{ auth()->check() ? route('crud.index', ['resource' => 'early-warnings']) : route('login') }}">Ver riesgo</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">INTERVENCIONES</h6>
                    <a class="btn btn-outline-primary" href="{{ auth()->check() ? route('crud.index', ['resource' => 'followup-interventions']) : route('login') }}">Ver intervenciones</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Inferior: 2 Columnas -->
    <div class="row g-3">
        <!-- Columna Izquierda: Evolución del Riesgo -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-4">EVOLUCIÓN DEL RIESGO</h6>
                    <div class="wireframe-box my-2">
                        <span class="text-secondary fw-semibold">Gráfico de líneas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Alertas Recientes -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-4">ALERTAS RECIENTES</h6>
                    <div class="d-flex flex-column gap-3 mt-2">
                        <div class="bg-secondary bg-opacity-20 rounded py-2 w-100"></div>
                        <div class="bg-secondary bg-opacity-20 rounded py-2 w-85" style="width: 85%;"></div>
                        <div class="bg-secondary bg-opacity-20 rounded py-2 w-75" style="width: 75%;"></div>
                        <div class="bg-secondary bg-opacity-20 rounded py-2 w-90" style="width: 90%;"></div>
                        <div class="bg-secondary bg-opacity-20 rounded py-2 w-60" style="width: 60%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection