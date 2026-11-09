<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return User::query()->orderBy('correo_inst')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'correo_inst' => ['required', 'email', 'max:120', 'unique:users,correo_inst'],
            'contrasena' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'in:estudiante,profesor,coordinador'],
            'estado' => ['sometimes', 'required', 'in:activo,inactivo'],
        ]);
        $data['contrasena'] = Hash::make($data['contrasena']);

        return response()->json(User::create($data), 201);
    }

    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'correo_inst' => ['sometimes', 'required', 'email', 'max:120', Rule::unique('users', 'correo_inst')->ignore($id, 'id_usuario')],
            'contrasena' => ['sometimes', 'required', 'string', 'min:8'],
            'rol' => ['sometimes', 'required', 'in:estudiante,profesor,coordinador'],
            'estado' => ['sometimes', 'required', 'in:activo,inactivo'],
        ]);
        if (isset($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }
        $user->update($data);

        return $user->refresh();
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->estudiante()->exists() || $user->profesor()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el usuario porque tiene un perfil asociado.'], 409);
        }

        $user->delete();

        return response()->noContent();
    }
}
