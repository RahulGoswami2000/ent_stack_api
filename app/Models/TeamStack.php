<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class TeamStack extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    public $table = 'team_stack';
    protected $fillable = [
        'company_id',
        'project_id',
        'company_stack_category_id',
        'company_stack_modules_id',
        'team_stack_data',
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

    public function project()
    {
        return $this->hasOne('App\Models\CompanyProject', 'id', 'project_id');
    }

    public function projectCategory()
    {
        return $this->hasOne('App\Models\ProjectCategory', 'id', 'project_category_id');
    }

    public function userAccess()
    {
        return $this->morphMany(UserStackAccess::class, "users", "stack_table_type", "stack_table_id");
    }
}
