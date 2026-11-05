<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class FollowupIntervention extends Model
{
    protected $table = 'intervencion_seguimiento';
    protected $primaryKey = 'id_intervencion';
    public $timestamps = false;

    protected $fillable = ['id_alerta', 'id_profesor', 'fecha_sesion', 'tipo_intervencion', 'observaciones', 'compromisos'];

    protected $casts = ['fecha_sesion' => 'date'];

    // RNF04: el historial es inmutable. Una intervención registrada no se edita ni se borra.
    // Si el equipo decide permitir correcciones, quitar este bloque y documentarlo en el A&D.
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Las intervenciones son inmutables (RNF04).'));
        static::deleting(fn () => throw new LogicException('Las intervenciones no se pueden eliminar (RNF04).'));
    }

    public function alerta()
    {
        return $this->belongsTo(EarlyWarning::class, 'id_alerta', 'id_alerta');
    }

    public function profesor()
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id_profesor');
    }
}
