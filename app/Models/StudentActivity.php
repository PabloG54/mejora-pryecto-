<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentActivity extends Model
{
    protected $table = 'student_activities';
    protected $primaryKey = 'id_actividad';
    public $timestamps = false;

    protected $fillable = [
        'id_estudiante', 'id_periodo', 'tipo_actividad', 'nombre_actividad',
        'organizacion_empresa', 'rol_o_actividades', 'evaluacion',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'id_estudiante');
    }

    public function periodo()
    {
        return $this->belongsTo(AcademicPeriod::class, 'id_periodo', 'id_periodo');
    }
}
