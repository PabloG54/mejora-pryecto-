<?php

namespace App\Http\Controllers;

use App\Models\FollowupIntervention;
use Illuminate\Http\Request;

class FollowupinterventionController extends Controller
{
    public function index()
    {
        return FollowupIntervention::query()->with(['alerta.estudiante', 'profesor.usuario'])->latest('fecha_sesion')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_alerta' => ['required', 'integer', 'exists:early_warnings,id_alerta'],
            'id_profesor' => ['required', 'integer', 'exists:teachers,id_profesor'],
            'fecha_sesion' => ['required', 'date_format:Y-m-d'],
            'tipo_intervencion' => ['required', 'in:tutoria,llamada,citacion,remision,otra'],
            'observaciones' => ['nullable', 'string'],
            'compromisos' => ['nullable', 'string'],
        ]);

        return response()->json(FollowupIntervention::create($data)->load(['alerta.estudiante', 'profesor.usuario']), 201);
    }

    public function show(string $id)
    {
        return FollowupIntervention::with(['alerta.estudiante.usuario', 'profesor.usuario'])->findOrFail($id);
    }
}
