<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Metric extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "metric";

    protected $fillable = [
        'name',
        'type',
        'metric_category_id',
        'format_of_matrix',
        'company_id',
        'expression',
        'expression_ids',
        'is_active',
        'is_admin',
        'can_delete',
        'expression_readable',
        'expression_data',
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

    public function category()
    {
        return $this->hasOne(MetricCategory::class, 'id', 'metric_category_id');
    }

    public function expressions()
    {
        $related = $this->hasMany(Metric::class);
        $related->setQuery(
            Metric::whereIn('id', $this->expression_ids ?? [])->getQuery()
        );

        return $related;
    }
}
