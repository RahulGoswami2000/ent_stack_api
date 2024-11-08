<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class SubscriptionHistory extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    public $table = "subscription_history";

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'id', 'subscription_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
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
