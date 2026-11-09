<?php

namespace App\Http\Controllers;

use App\Models\EarlyWarning;
use Illuminate\Http\Request;

class EarlyWarningController extends Controller
{
    public function index()
    {
        return EarlyWarning::query()->with(['estudiante', 'periodo'])->latest('fecha_reporte')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_estudiante' => ['required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_riesgo' => ['required', 'in:academico,inasistencia,socioeconomico,bienestar'],
            'nivel_riesgo' => ['required', 'in:bajo,medio,alto'],
            'fecha_reporte' => ['required', 'date_format:Y-m-d'],
            'estado' => ['sometimes', 'required', 'in:abierta,en_seguimiento,cerrada'],
        ]);

        return response()->json(EarlyWarning::create($data)->load(['estudiante', 'periodo']), 201);
    }

    public function show(string $id)
    {
        return EarlyWarning::with(['estudiante.usuario', 'periodo', 'intervenciones.profesor.usuario'])->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $warning = EarlyWarning::findOrFail($id);
        $data = $request->validate([
            'id_estudiante' => ['sometimes', 'required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['sometimes', 'required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_riesgo' => ['sometimes', 'required', 'in:academico,inasistencia,socioeconomico,bienestar'],
            'nivel_riesgo' => ['sometimes', 'required', 'in:bajo,medio,alto'],
            'fecha_reporte' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'estado' => ['sometimes', 'required', 'in:abierta,en_seguimiento,cerrada'],
        ]);
        $warning->update($data);

        return $warning->refresh()->load(['estudiante', 'periodo']);
    }

    public function destroy(string $id)
    {
        $warning = EarlyWarning::findOrFail($id);
        if ($warning->intervenciones()->exists()) {
            return response()->json(['message' => 'No se puede eliminar la alerta porque tiene intervenciones asociadas.'], 409);
        }

        $warning->delete();

        return response()->noContent();
    }
}
