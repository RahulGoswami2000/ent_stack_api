<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScorecardStackAudit extends Model
{
    public $table = 'scorecard_stack_audit';

    protected $fillable = [
        'scorecard_stack_id',
        'old_scorecard_data',
        'new_scorecard_data'
    ];
}
