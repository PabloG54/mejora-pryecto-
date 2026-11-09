<?php

namespace App\Http\Controllers;

use App\Models\StudentActivity;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function index()
    {
        return StudentActivity::query()->with(['estudiante', 'periodo'])->latest('id_actividad')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_estudiante' => ['required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_actividad' => ['required', 'in:semillero,investigacion,monitoria,beca,publicacion,evento,cultural,deportiva,practica,otra'],
            'nombre_actividad' => ['required', 'string', 'max:150'],
            'organizacion_empresa' => ['nullable', 'string', 'max:150'],
            'rol_o_actividades' => ['nullable', 'string', 'max:255'],
            'evaluacion' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(StudentActivity::create($data)->load(['estudiante', 'periodo']), 201);
    }

    public function show(string $id)
    {
        return StudentActivity::with(['estudiante.usuario', 'periodo'])->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $activity = StudentActivity::findOrFail($id);
        $data = $request->validate([
            'id_estudiante' => ['sometimes', 'required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['sometimes', 'required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_actividad' => ['sometimes', 'required', 'in:semillero,investigacion,monitoria,beca,publicacion,evento,cultural,deportiva,practica,otra'],
            'nombre_actividad' => ['sometimes', 'required', 'string', 'max:150'],
            'organizacion_empresa' => ['sometimes', 'nullable', 'string', 'max:150'],
            'rol_o_actividades' => ['sometimes', 'nullable', 'string', 'max:255'],
            'evaluacion' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);
        $activity->update($data);

        return $activity->refresh()->load(['estudiante', 'periodo']);
    }

    public function destroy(string $id)
    {
        StudentActivity::findOrFail($id)->delete();

        return response()->noContent();
    }
}
