<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class UserStackAccess extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = 'user_stack_access';

    protected $fillable = [
        'user_id',
        'company_id',
        'project_id',
        'company_stack_modules_id',
        'company_stack_category_id',
        'stack_table_id',
        'stack_table_type',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function users()
    {
        $this->morphTo();
    }

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
}
