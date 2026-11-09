<?php

namespace App\Models;

#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';
    protected $primaryKey = 'id_profesor';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'departamento', 'facultad'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function intervenciones()
    {
        return $this->hasMany(FollowupIntervention::class, 'id_profesor', 'id_profesor');
    }
}
