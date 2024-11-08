<?php

namespace App\Models;

use App\Models\Role;
use Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject,
    Auditable
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable, SoftDeletes, AuditableTrait;

    public $primaryKey = 'id';

    public $incrementing = true;

    public $table = "mst_users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'country_code',
        'mobile_no',
        'password',
        'role_id',
        'job_role',
        'date_of_birth',
        'start_date',
        'country_code',
        'privileges',
        'profile_image',
        'user_type',
        'is_active',
        'client_assigned',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];


    /**
     * For Authentication
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * For Authentication
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id'            => $this->id,
                'first_name'    => $this->first_name,
                'last_name'     => $this->last_name,
                'email_id'      => $this->email_id,
                'mobile_no'     => $this->mobile_no,
                'profile_image' => $this->profile_image,
            ],
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }
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

    public function role()
    {
        return $this->hasOne(\App\Models\Role::class, 'id', 'role_id');
    }

    public function companyMatrix()
    {
        return $this->hasMany(CompanyMatrix::class, 'user_id', 'id')->select(['mst_company.id as company_id', 'mu.id as user_id', 'mu.email', \DB::raw("CONCAT(mu.first_name,' ',mu.last_name) as name"), 'mu.mobile_no'])->leftjoin('mst_company', 'mst_company.id', '=', 'mst_user_company_matrix.company_id')
            ->leftjoin('mst_users as mu', 'mu.id', '=', 'mst_company.user_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'user_id', 'id');
    }

    public function companyMatrixList()
    {
        return $this->hasMany(CompanyMatrix::class, 'user_id', 'id');
    }

    public function referClient()
    {
        return $this->hasMany(ReferClient::class, 'created_by', 'id');
    }

    public function stackAccessRights()
    {
        return $this->hasMany(UserStackAccess::class, 'user_id', 'id');
    }
}
