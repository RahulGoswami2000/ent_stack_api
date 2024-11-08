<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ReferClient extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;
    public $table = 'refer_client';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'referal_code',
        'is_referred',
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
        return $this->hasOne(Company::class, 'refer_client_id', 'id');
    }
}
