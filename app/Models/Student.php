<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'codigo_estud', 'doc_identidad', 'nombres', 'apellidos',
        'colegio_origen', 'puntaje_saber11', 'prueba_admision', 'salud_fisica_mental',
    ];

    // RNF02: los datos de salud se guardan cifrados.
    protected $casts = [
        'salud_fisica_mental' => 'encrypted',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function matriculas()
    {
        return $this->hasMany(Tuition::class, 'id_estudiante', 'id_estudiante');
    }

    public function actividades()
    {
        return $this->hasMany(StudentActivity::class, 'id_estudiante', 'id_estudiante');
    }

    public function solicitudes()
    {
        return $this->hasMany(AcademicRequest::class, 'id_estudiante', 'id_estudiante');
    }

    public function alertas()
    {
        return $this->hasMany(EarlyWarning::class, 'id_estudiante', 'id_estudiante');
    }
}
