<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ScorecardStackArchive extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "scorecard_stack_archive";

    protected $fillable = [
        'type',
        'company_id',
        'project_id',
        'company_stack_modules_id',
        'company_stack_category_id',
        'scorecard_stack_id',
        'node_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}
