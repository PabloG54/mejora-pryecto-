<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        return Teacher::query()->with('usuario')->orderBy('departamento')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'integer', 'exists:users,id_usuario', 'unique:teachers,id_usuario'],
            'departamento' => ['required', 'string', 'max:80'],
            'facultad' => ['required', 'string', 'max:80'],
        ]);

        return response()->json(Teacher::create($data)->load('usuario'), 201);
    }

    public function show(string $id)
    {
        return Teacher::with(['usuario', 'intervenciones.alerta'])->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $data = $request->validate([
            'id_usuario' => ['sometimes', 'required', 'integer', 'exists:users,id_usuario', Rule::unique('teachers', 'id_usuario')->ignore($id, 'id_profesor')],
            'departamento' => ['sometimes', 'required', 'string', 'max:80'],
            'facultad' => ['sometimes', 'required', 'string', 'max:80'],
        ]);
        $teacher->update($data);

        return $teacher->refresh()->load('usuario');
    }

    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        if ($teacher->intervenciones()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el profesor porque tiene intervenciones asociadas.'], 409);
        }

        $teacher->delete();

        return response()->noContent();
    }
}
