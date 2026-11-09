<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        return AcademicPeriod::query()->orderBy('fecha_inicio', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_periodo' => ['required', 'string', 'max:30', 'unique:academic_periods,nombre_periodo'],
            'fecha_inicio' => ['required', 'date_format:Y-m-d'],
            'fecha_fin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecha_inicio'],
        ]);

        return response()->json(AcademicPeriod::create($data), 201);
    }

    public function show(string $id)
    {
        return AcademicPeriod::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $period = AcademicPeriod::findOrFail($id);
        $request->merge([
            'fecha_inicio' => $request->input('fecha_inicio', $period->fecha_inicio->toDateString()),
            'fecha_fin' => $request->input('fecha_fin', $period->fecha_fin->toDateString()),
        ]);
        $data = $request->validate([
            'nombre_periodo' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('academic_periods', 'nombre_periodo')->ignore($id, 'id_periodo')],
            'fecha_inicio' => ['required', 'date_format:Y-m-d'],
            'fecha_fin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecha_inicio'],
        ]);
        $period->update($data);

        return $period->refresh();
    }

    public function destroy(string $id)
    {
        $period = AcademicPeriod::findOrFail($id);
        if ($period->matriculas()->exists() || $period->alertas()->exists()
            || $period->actividades()->exists() || $period->solicitudes()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el periodo porque tiene registros asociados.'], 409);
        }

        $period->delete();

        return response()->noContent();
    }
}
