<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
#use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = ['correo_inst', 'contrasena', 'rol', 'estado'];
    protected $hidden = ['contrasena'];

    // Laravel usa este método para validar la contraseña en el login.
    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    public function estudiante()
    {
        return $this->hasOne(Student::class, 'id_usuario', 'id_usuario');
    }

    public function profesor()
    {
        return $this->hasOne(Teacher::class, 'id_usuario', 'id_usuario');
    }
    
}
