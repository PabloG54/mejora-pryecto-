<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        return Student::query()->with('usuario')->orderBy('apellidos')->orderBy('nombres')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'integer', 'exists:users,id_usuario', 'unique:students,id_usuario'],
            'codigo_estud' => ['required', 'string', 'max:20', 'unique:students,codigo_estud'],
            'doc_identidad' => ['required', 'string', 'max:20', 'unique:students,doc_identidad'],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'colegio_origen' => ['nullable', 'string', 'max:120'],
            'puntaje_saber11' => ['nullable', 'integer', 'between:0,500'],
            'prueba_admision' => ['nullable', 'integer', 'between:0,65535'],
            'salud_fisica_mental' => ['nullable', 'string'],
        ]);

        return response()->json(Student::create($data)->load('usuario'), 201);
    }

    public function show(string $id)
    {
        return Student::with(['usuario', 'matriculas.asignatura', 'matriculas.periodo', 'actividades.periodo', 'solicitudes.periodo', 'alertas.periodo'])
            ->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        $data = $request->validate([
            'id_usuario' => ['sometimes', 'required', 'integer', 'exists:users,id_usuario', Rule::unique('students', 'id_usuario')->ignore($id, 'id_estudiante')],
            'codigo_estud' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('students', 'codigo_estud')->ignore($id, 'id_estudiante')],
            'doc_identidad' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('students', 'doc_identidad')->ignore($id, 'id_estudiante')],
            'nombres' => ['sometimes', 'required', 'string', 'max:80'],
            'apellidos' => ['sometimes', 'required', 'string', 'max:80'],
            'colegio_origen' => ['sometimes', 'nullable', 'string', 'max:120'],
            'puntaje_saber11' => ['sometimes', 'nullable', 'integer', 'between:0,500'],
            'prueba_admision' => ['sometimes', 'nullable', 'integer', 'between:0,65535'],
            'salud_fisica_mental' => ['sometimes', 'nullable', 'string'],
        ]);
        $student->update($data);

        return $student->refresh()->load('usuario');
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        if ($student->matriculas()->exists() || $student->actividades()->exists()
            || $student->solicitudes()->exists() || $student->alertas()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el estudiante porque tiene registros asociados.'], 409);
        }

        $student->delete();

        return response()->noContent();
    }
}
