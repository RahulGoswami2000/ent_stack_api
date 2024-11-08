<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyMatrix;
use App\Library\FunctionUtils;
use App\Models\ReferClient;
use App\Models\SubscriptionHistory;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;
use App\Traits\CommonTrait;

class ApiIntegrationRepository extends BaseRepository
{
    use CommonTrait;

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }


    /**
     * Registration
     */
    public function store($request)
    {
        $storeData = [
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'country_code' => "",
            'mobile_no'    => $request->mobile_no,
            'email'        => $request->email,
            'password'     => bcrypt($request->password),
            'role_id'      => 2,
            'is_active'    => 1,
        ];

        if (!empty($request['profile_image'])) {
            $fileName = FunctionUtils::uploadFileOnS3($request['profile_image'], config('global.UPLOAD_PATHS.USER_PROFILE'));
            if (!empty($fileName)) {
                $storeData['profile_image'] = $fileName;
            }
        }
        $userInsert = User::create($storeData);

        $companyInsert       = Company::create([
            'user_id'      => $userInsert->id,
            'company_name' => 'N/A',
            'is_active'    => 1,
        ]);
        $companyMatrixInsert = CompanyMatrix::create([
            'user_id'     => $userInsert->id,
            'company_id'  => $companyInsert->id,
            'role_id'     => 2,
            'is_accepted' => 1,
        ]);
        if (!empty($request->refer_code)) {
            $referCode = ReferClient::select('refer_client.id')->where('refer_client.referal_code', $request->refer_code)->first();
            if (!empty($referCode)) {
                $companyInsert->update([
                    'refer_client_id' => $referCode->id,
                ]);
            }
        }

        $data = ['User' => $userInsert, 'Company' => $companyInsert, 'Company Matrix' => $companyMatrixInsert];
        return ['data' => $data];
    }

    public function subscriptionPayment($request)
    {
        return SubscriptionHistory::create([
            'user_id'         => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'amount'          => $request->amount,
        ]);
    }

    public function subscriptionList()
    {
        $query = \DB::table('subscriptions')
            ->select('subscriptions.id', 'subscriptions.name', 'subscriptions.description', 'subscriptions.amount', 'subscriptions.is_active')
            ->whereNull('subscriptions.deleted_at')->get()->toArray();
        return $query;
    }
}
