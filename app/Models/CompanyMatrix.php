<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyMatrix extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table  = "mst_user_company_matrix";

    protected $fillable = [
        'user_id',
        'company_id',
        'is_active',
        'role_id',
        'privileges',
        'is_accepted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function createdBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }

    public function deletedBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'deleted_by');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
