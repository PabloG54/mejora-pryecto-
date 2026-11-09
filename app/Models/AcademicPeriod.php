<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    protected $table = 'academic_periods';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    protected $fillable = ['nombre_periodo', 'fecha_inicio', 'fecha_fin'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function matriculas()
    {
        return $this->hasMany(Tuition::class, 'id_periodo', 'id_periodo');
    }

    public function alertas()
    {
        return $this->hasMany(EarlyWarning::class, 'id_periodo', 'id_periodo');
    }

    public function actividades()
    {
        return $this->hasMany(StudentActivity::class, 'id_periodo', 'id_periodo');
    }

    public function solicitudes()
    {
        return $this->hasMany(AcademicRequest::class, 'id_periodo', 'id_periodo');
    }
}
