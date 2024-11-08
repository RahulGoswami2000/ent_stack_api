<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ScorecardStackNodeData extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "scorecard_stack_node_data";

    protected $fillable = [
        'scorecard_stack_id',
        'node_id',
        'value',
        'comment',
        'assigned_color',
        'from_date',
        'to_date',
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
}
