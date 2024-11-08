<?php

namespace App\Services\Web;

use App\Repositories\Web\UserRepository;
use App\Services\BaseService;
use App\Http\Requests\Auth\User\UserRequest;

class UserService extends BaseService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    /**
     * List Users
     */
    public function list()
    {
        return $this->userRepository->list();
    }

    /**
     * Users Update
     */
    public function update($id, $postData)
    {
        return $this->userRepository->update($id, $postData);
    }

    /**
     * Users Details
     */
    public function details($id, $companyId = null)
    {
        return $this->userRepository->details($id, $companyId);
    }

    /**
     * Users Status
     */
    public function changeStatus($id, $request)
    {
        return $this->userRepository->changeStatus($id, $request);
    }

    public function setOtp($postData)
    {
        return $this->userRepository->setOtp($postData);
    }

    public function checkOtpExists($otp)
    {
        return $this->userRepository->checkOtpExists($otp);
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function getUserByPhoneNumber($phoneNUmber)
    {
        return $this->userRepository->getUserByPhoneNumber($phoneNUmber);
    }

    public function setPassword($user, $password)
    {
        return $this->userRepository->setPassword($user, $password);
    }

    public function deleteOtp($otp)
    {
        return $this->userRepository->deleteOtp($otp);
    }

    public function updateProfile($id, $request)
    {
        return $this->userRepository->updateProfile($id, $request);
    }

    public function leaveOrganization($id)
    {
        return $this->userRepository->leaveOrganization($id);
    }

    public function profileImage($id, $request)
    {
        return $this->userRepository->profileImage($id, $request);
    }

    public function assignStacks($request)
    {
        return $this->userRepository->assignStacks($request);
    }

    public function verifyUserChangePassword($user, $password)
    {
        return $this->userRepository->verifyUserChangePassword($user, $password);
    }

    public function checkUserIsAssociatedWithCompany($userId, $companyId)
    {
        return $this->userRepository->checkUserIsAssociatedWithCompany($userId, $companyId);
    }
}
