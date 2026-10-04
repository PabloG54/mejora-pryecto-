@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <h2 class="fw-bold fs-4 mb-4 text-dark">Dashboard</h2>

    <!-- Fila de 4 Tarjetas Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">ESTUDIANTES</h6>
                    <div class="bg-secondary bg-opacity-20 rounded py-3 px-2 w-50"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">ALERTAS ACTIVAS</h6>
                    <div class="bg-secondary bg-opacity-20 rounded py-3 px-2 w-50"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">RIESGO ALTO</h6>
                    <div class="bg-secondary bg-opacity-20 rounded py-3 px-2 w-50"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 p-2">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary fw-bold small mb-3">INTERVENCIONES</h6>
                    <div class="bg-secondary bg-opacity-20 rounded py-3 px-2 w-50"></div>
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