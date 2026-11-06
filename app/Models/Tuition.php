<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuition extends Model
{
    protected $table = 'matricula';
    protected $primaryKey = 'id_matricula';
    public $timestamps = false;

    protected $fillable = ['id_estudiante', 'id_asignatura', 'id_periodo', 'nota_final', 'estado_materia'];

    protected $casts = ['nota_final' => 'decimal:1'];

    public function estudiante()
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'id_estudiante');
    }

    public function asignatura()
    {
        return $this->belongsTo(Course::class, 'id_asignatura', 'id_asignatura');
    }

    public function periodo()
    {
        return $this->belongsTo(AcademicPeriod::class, 'id_periodo', 'id_periodo');
    }
}
