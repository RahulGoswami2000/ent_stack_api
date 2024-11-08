<?php

namespace App\Services\Admin;

use App\Repositories\Admin\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{

    private $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Set Otp
     */
    public function setOtp($postData)
    {
        return $this->userRepository->setOtp($postData);
    }

    /**
     * Check Otp Exists
     */
    public function checkOtpExists($otp)
    {
        return $this->userRepository->checkOtpExists($otp);
    }

    /**
     * Get User by email
     */
    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    /**
     * Get User by phonenumber
     */
    public function getUserByPhoneNumber($phoneNUmber)
    {
        return $this->userRepository->getUserByPhoneNumber($phoneNUmber);
    }

    /**
     * Set Password
     */
    public function setPassword($user, $password)
    {
        return $this->userRepository->setPassword($user, $password);
    }

    /**
     *
     */
    public function deleteOtp($otp)
    {
        return $this->userRepository->deleteOtp($otp);
    }

    public function updateProfile($id, $request)
    {
        return $this->userRepository->updateProfile($id, $request);
    }
    /**
     *User Details
     */
    public function details($id)
    {
        return $this->userRepository->details($id);
    }
}
