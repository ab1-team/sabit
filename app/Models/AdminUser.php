<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class AdminUser extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $connection = 'central';

    protected $table = 'admin_user';

    protected $fillable = [
        'tenant_id',
        'nama_lengkap',
        'email',
        'password',
        'akses',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function invoices()
    {
        return $this->hasMany(AdminInvoice::class, 'user_id');
    }
}
