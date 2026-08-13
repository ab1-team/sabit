<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = ['id'];

    protected $fillable = [
        'nama',
        'nik',
        'id_jabatan',
        'jenis_kelamin',
        'telepon',
        'alamat',
        'foto',
        'email',
        'username',
        'password',
        'hak_akses',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'hak_akses' => 'array',
    ];

    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }
}
