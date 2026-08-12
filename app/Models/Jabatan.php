<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_jabatan');
    }
}
