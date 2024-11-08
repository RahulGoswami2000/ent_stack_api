<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyProject extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    public $table = 'company_projects';
    protected $fillable = [
        'company_id',
        'name',
        'sequence',
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

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function companyStackModule()
    {
        return $this->hasMany(CompanyStackModules::class, 'project_id', 'id');
    }
}
