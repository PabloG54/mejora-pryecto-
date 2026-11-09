<?php

namespace App\Http\Controllers;

use App\Models\AcademicRequest;
use Illuminate\Http\Request;

class AcademicRequestController extends Controller
{
    public function index()
    {
        return AcademicRequest::query()->with(['estudiante', 'periodo'])->latest('fecha_radicacion')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_estudiante' => ['required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_solicitud' => ['required', 'in:cancelacion,reintegro,homologacion,excusa,otra'],
            'fecha_radicacion' => ['required', 'date_format:Y-m-d'],
            'estado' => ['sometimes', 'required', 'in:radicada,en_tramite,aprobada,rechazada'],
        ]);

        return response()->json(AcademicRequest::create($data)->load(['estudiante', 'periodo']), 201);
    }

    public function show(string $id)
    {
        return AcademicRequest::with(['estudiante.usuario', 'periodo'])->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $requestRecord = AcademicRequest::findOrFail($id);
        $data = $request->validate([
            'id_estudiante' => ['sometimes', 'required', 'integer', 'exists:students,id_estudiante'],
            'id_periodo' => ['sometimes', 'required', 'integer', 'exists:academic_periods,id_periodo'],
            'tipo_solicitud' => ['sometimes', 'required', 'in:cancelacion,reintegro,homologacion,excusa,otra'],
            'fecha_radicacion' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'estado' => ['sometimes', 'required', 'in:radicada,en_tramite,aprobada,rechazada'],
        ]);
        $requestRecord->update($data);

        return $requestRecord->refresh()->load(['estudiante', 'periodo']);
    }

    public function destroy(string $id)
    {
        AcademicRequest::findOrFail($id)->delete();

        return response()->noContent();
    }
}
