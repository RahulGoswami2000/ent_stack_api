<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyStackCategory extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = 'company_stack_category';

    protected $fillable = [
        'company_id',
        'project_id',
        'company_stack_modules_id',
        'name',
        'sequence',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
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
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
