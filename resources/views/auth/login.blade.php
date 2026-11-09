@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-2">Iniciar sesión</h1>
                <p class="text-secondary mb-4">Ingresa para administrar y probar los recursos de SAAT.</p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="correo_inst">Correo institucional</label>
                        <input class="form-control @error('correo_inst') is-invalid @enderror" id="correo_inst" name="correo_inst" type="email"
                            value="{{ old('correo_inst') }}" autocomplete="username" aria-describedby="correo_inst-error" required autofocus>
                        @error('correo_inst')<div class="invalid-feedback" id="correo_inst-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Contraseña</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password"
                            autocomplete="current-password" aria-describedby="password-error" required>
                        @error('password')<div class="invalid-feedback" id="password-error">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Entrar</button>
                </form>

                @if (app()->environment(['local', 'testing']) && ! \App\Models\User::query()->exists())
                    <p class="small text-secondary mt-4 mb-0">
                        ¿Primera ejecución?
                        <a href="{{ route('setup') }}">Crear la cuenta inicial de coordinación</a>.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
