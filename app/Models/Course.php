<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id_asignatura';
    public $timestamps = false;

    protected $fillable = ['codigo_asig', 'nombre_asig', 'programa_academico', 'creditos'];

    public function matriculas()
    {
        return $this->hasMany(Tuition::class, 'id_asignatura', 'id_asignatura');
    }
}
