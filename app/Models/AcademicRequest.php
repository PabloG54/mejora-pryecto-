<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicRequest extends Model
{
    protected $table = 'solicitud_academica';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    protected $fillable = ['id_estudiante', 'id_periodo', 'tipo_solicitud', 'fecha_radicacion', 'estado'];

    protected $casts = ['fecha_radicacion' => 'date'];

    public function estudiante()
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'id_estudiante');
    }

    public function periodo()
    {
        return $this->belongsTo(AcademicPeriod::class, 'id_periodo', 'id_periodo');
    }
}
