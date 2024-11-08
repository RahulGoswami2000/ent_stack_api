<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table  = "mst_company";

    protected $fillable = [
        'user_id',
        'company_name',
        'website_url',
        'company_logo',
        'refer_client_id',
        'is_active',
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
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
    public function userId()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function companyUser()
    {
        return $this->hasMany(CompanyMatrix::class, 'company_id', 'id');
    }

    public function companyProject()
    {
        return $this->hasMany(CompanyProject::class, 'company_id', 'id');
    }
}
