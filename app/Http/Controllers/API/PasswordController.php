<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;

use App\Services\API\PasswordService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Http\Requests\API\ResetPasswordRequest;


class PasswordController extends Controller
{

    /** @var App\Services\API\PasswordService */
    private $passwordService;

    /**
     * PasswordController constructor.
     *
     * @param App\Services\API\PasswordService $passwordService
     */
    public function __construct(PasswordService $passwordService)
    {
        parent::__construct();
        $this->passwordService = $passwordService;
    }

    /**
     * Handles the forgot password request
     *
     * @param Request $request
     * @return Response
     */
    public function forgot(ForgotPasswordRequest $request)
    {
        $request->validated();

        try {
            $result = $this->passwordService->forgotPassword($request->getEmailAddress());
            $this->response['token'] = $result->token;
        } catch (Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }

        return response()->json($this->response, $this->response['code']);
    }

    /**
     * Handles the reset password request
     *
     * @param Request $request
     * @return Response
     */
    public function reset(ResetPasswordRequest $request)
    {
        $request->validated();

        try {
            $formData = [
                'token' => $request->getToken(),
                'password' => $request->getPassword2(),
            ];

            // perform password reset
            $this->passwordService->reset($formData);
            $this->response['reset'] = true;
        } catch (Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }

        return response()->json($this->response, $this->response['code']);
    }
    
}
