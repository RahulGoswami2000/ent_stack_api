<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ScorecardStackNodes extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "scorecard_stack_nodes";

    protected $fillable = [
        'scorecard_stack_id',
        'node_id',
        'node_data',
        'auto_assign_color',
        'assigned_to',
        'assigned_color',
        'goal_achieve_in_number',
        'reminder',
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

    public function assignedToUser()
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }
}
