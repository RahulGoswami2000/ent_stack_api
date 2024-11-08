<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class MetricGroupMatrix extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = 'metric_group_matrix';

    public $fillable = [
        'metric_id',
        'metric_group_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function metric()
    {
        return $this->hasOne(Metric::class, 'id', 'metric_id');
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
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
