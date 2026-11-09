<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        return Course::query()->orderBy('nombre_asig')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_asig' => ['required', 'string', 'max:20', 'unique:courses,codigo_asig'],
            'nombre_asig' => ['required', 'string', 'max:120'],
            'programa_academico' => ['required', 'string', 'max:80'],
            'creditos' => ['required', 'integer', 'between:0,255'],
        ]);

        return response()->json(Course::create($data), 201);
    }

    public function show(string $id)
    {
        return Course::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);
        $data = $request->validate([
            'codigo_asig' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('courses', 'codigo_asig')->ignore($id, 'id_asignatura')],
            'nombre_asig' => ['sometimes', 'required', 'string', 'max:120'],
            'programa_academico' => ['sometimes', 'required', 'string', 'max:80'],
            'creditos' => ['sometimes', 'required', 'integer', 'between:0,255'],
        ]);
        $course->update($data);

        return $course->refresh();
    }

    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        if ($course->matriculas()->exists()) {
            return response()->json(['message' => 'No se puede eliminar la asignatura porque tiene matrículas asociadas.'], 409);
        }

        $course->delete();

        return response()->noContent();
    }
}
