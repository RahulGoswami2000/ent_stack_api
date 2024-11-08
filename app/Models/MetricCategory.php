<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class MetricCategory extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "metric_categories";

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
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

    public function metrics()
    {
        return $this->hasMany(Metric::class, 'metric_category_id', 'id');
    }
}
