<?php

namespace App\Models\Tenant;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class TenantAdminUser extends Model implements Authenticatable
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
        return $this->hasMany(TenantInvoice::class, 'user_id');
    }
}

