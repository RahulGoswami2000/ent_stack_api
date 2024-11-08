<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\Company;
use PhpParser\Node\Expr\FuncCall;

class OrganizationRepository extends BaseRepository
{
    private $company;

    public function __construct()
    {
        $this->company = new Company();
    }

    public function details($id)
    {
        $dataDetails = $this->company->find($id);
        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    public function update($id, $request)
    {
        $data = $this->company->find($id);
        if (empty($data)) {
            return null;
        }

        $data->update([
            'company_name' => $request->company_name,
            'website_url'  => $request->url,
        ]);

        return $data;
    }

    public function changeLogo($id, $request)
    {
        $data = $this->company->findOrFail($id);
        $updateData = [];
        if (empty($data)) {
            return null;
        }

        if ($request->has('logo') && !empty($request->logo)) {
            $fileName = FunctionUtils::uploadFileOnS3($request->logo, config('global.UPLOAD_PATHS.COMPANY_LOGO'), $data->company_logo);
            if (!empty($fileName)) {
                $updateData['company_logo'] = $fileName;
            }
        } else {
            $updateData['company_logo'] = NULL;
        }

        $data->update($updateData);
        if (!empty($request->logo)) {
            $data->company_logo = FunctionUtils::getS3FileUrl(config('global.UPLOAD_PATHS.COMPANY_LOGO') . $updateData['company_logo']);
        } else {
            $data->company_logo = null;
        }
        return $data;
    }
}
