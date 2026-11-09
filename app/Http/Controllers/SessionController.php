<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('crud.index');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'correo_inst' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials)) {
            return back()->withErrors([
                'correo_inst' => 'Las credenciales no son válidas.',
            ])->onlyInput('correo_inst');
        }

        if (Auth::guard('web')->user()->estado !== 'activo') {
            Auth::guard('web')->logout();

            return back()->withErrors([
                'correo_inst' => 'La cuenta está inactiva.',
            ])->onlyInput('correo_inst');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('crud.index'));
    }

    public function setup(): View
    {
        $this->ensureSetupIsAvailable();

        return view('auth.setup');
    }

    public function storeSetup(Request $request): RedirectResponse
    {
        $this->ensureSetupIsAvailable();

        $data = $request->validate([
            'correo_inst' => ['required', 'email', 'max:120', 'unique:users,correo_inst'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'correo_inst' => $data['correo_inst'],
            'contrasena' => Hash::make($data['password']),
            'rol' => 'coordinador',
            'estado' => 'activo',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('crud.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::forgetGuards();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function ensureSetupIsAvailable(): void
    {
        abort_unless(
            app()->environment(['local', 'testing']) && ! User::query()->exists(),
            404
        );
    }
}
