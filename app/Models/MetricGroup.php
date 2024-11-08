<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class MetricGroup extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "metric_groups";

    protected $fillable = [
        'name',
        'metric_category_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
    public function deletedBy()
    {
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
    public function category()
    {
        return $this->hasOne(MetricCategory::class, 'id', 'metric_category_id');
    }

    public function metricBox()
    {
        return $this->hasMany(MetricGroupMatrix::class, 'metric_group_id', 'id');
    }
}
