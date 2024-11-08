<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ScorecardStack extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    public $table = 'scorecard_stack';
    protected $fillable = [
        'company_id',
        'project_id',
        'company_stack_module_id',
        'company_stack_category_id',
        'scorecard_type',
        'scorecard_start_from',
        'scorecard_data',
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

    public function scorecardStackNodes()
    {
        return $this->hasMany(ScorecardStackNodes::class, "scorecard_stack_id", "id");
    }

    public function scorecardStackNodeData()
    {
        return $this->hasMany(ScorecardStackNodeData::class, "scorecard_stack_id", "id");
    }

    public function transformAudit(array $data): array
    {
        $dataDetails = ScorecardStackAudit::create([
            'scorecard_stack_id' => $data['auditable_id'],
            'new_scorecard_data' => json_encode($data['new_values']),
            'old_scorecard_data' => json_encode($data['old_values']),
        ]);
        $data['new_values'] = $dataDetails->id;
        $data['old_values'] = null;
        return $data;
    }
}
