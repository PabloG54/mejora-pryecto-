<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarlyWarning extends Model
{
    protected $table = 'alerta_temprana';
    protected $primaryKey = 'id_alerta';
    public $timestamps = false;

    protected $fillable = ['id_estudiante', 'id_periodo', 'tipo_riesgo', 'nivel_riesgo', 'fecha_reporte', 'estado'];

    protected $casts = ['fecha_reporte' => 'date'];

    public function estudiante()
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'id_estudiante');
    }

    public function periodo()
    {
        return $this->belongsTo(AcademicPeriod::class, 'id_periodo', 'id_periodo');
    }

    public function intervenciones()
    {
        return $this->hasMany(FollowupIntervention::class, 'id_alerta', 'id_alerta');
    }
}
