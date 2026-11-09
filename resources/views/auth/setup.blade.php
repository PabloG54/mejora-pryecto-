@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-2">Configurar SAAT</h1>
                <p class="text-secondary mb-4">Crea la primera cuenta de coordinación para iniciar sesión. Esta configuración solo está disponible en desarrollo y antes de registrar usuarios.</p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('setup') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="correo_inst">Correo institucional</label>
                        <input class="form-control @error('correo_inst') is-invalid @enderror" id="correo_inst" name="correo_inst" type="email"
                            value="{{ old('correo_inst') }}" autocomplete="username" aria-describedby="correo_inst-error" required autofocus>
                        @error('correo_inst')<div class="invalid-feedback" id="correo_inst-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña (mínimo 8 caracteres)</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password"
                            autocomplete="new-password" aria-describedby="password-error" minlength="8" required>
                        @error('password')<div class="invalid-feedback" id="password-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" type="password"
                            autocomplete="new-password" aria-describedby="password_confirmation-error" minlength="8" required>
                        @error('password_confirmation')<div class="invalid-feedback" id="password_confirmation-error">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Crear cuenta y entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
