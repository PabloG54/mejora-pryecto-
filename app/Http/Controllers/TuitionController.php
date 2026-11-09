<?php

namespace App\Http\Controllers;

use App\Models\Tuition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TuitionController extends Controller
{
    public function index()
    {
        return Tuition::query()->with(['estudiante', 'asignatura', 'periodo'])->latest('id_matricula')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_estudiante' => [
                'required', 'integer', 'exists:students,id_estudiante',
                Rule::unique('tuitions', 'id_estudiante')->where(fn ($query) => $query
                    ->where('id_asignatura', $request->input('id_asignatura'))
                    ->where('id_periodo', $request->input('id_periodo'))),
            ],
            'id_asignatura' => ['required', 'integer', 'exists:courses,id_asignatura'],
            'id_periodo' => ['required', 'integer', 'exists:academic_periods,id_periodo'],
            'nota_final' => ['nullable', 'numeric', 'decimal:0,1', 'between:0,5'],
            'estado_materia' => ['sometimes', 'required', 'in:inscrita,aprobada,reprobada,cancelada'],
        ]);

        return response()->json(Tuition::create($data)->load(['estudiante', 'asignatura', 'periodo']), 201);
    }

    public function show(string $id)
    {
        return Tuition::with(['estudiante.usuario', 'asignatura', 'periodo'])->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $tuition = Tuition::findOrFail($id);
        $request->merge([
            'id_estudiante' => $request->input('id_estudiante', $tuition->id_estudiante),
            'id_asignatura' => $request->input('id_asignatura', $tuition->id_asignatura),
            'id_periodo' => $request->input('id_periodo', $tuition->id_periodo),
        ]);
        $data = $request->validate([
            'id_estudiante' => [
                'required', 'integer', 'exists:students,id_estudiante',
                Rule::unique('tuitions', 'id_estudiante')
                    ->where(fn ($query) => $query
                        ->where('id_asignatura', $request->input('id_asignatura'))
                        ->where('id_periodo', $request->input('id_periodo')))
                    ->ignore($id, 'id_matricula'),
            ],
            'id_asignatura' => ['required', 'integer', 'exists:courses,id_asignatura'],
            'id_periodo' => ['required', 'integer', 'exists:academic_periods,id_periodo'],
            'nota_final' => ['sometimes', 'nullable', 'numeric', 'decimal:0,1', 'between:0,5'],
            'estado_materia' => ['sometimes', 'required', 'in:inscrita,aprobada,reprobada,cancelada'],
        ]);
        $tuition->update($data);

        return $tuition->refresh()->load(['estudiante', 'asignatura', 'periodo']);
    }

    public function destroy(string $id)
    {
        Tuition::findOrFail($id)->delete();

        return response()->noContent();
    }
}
