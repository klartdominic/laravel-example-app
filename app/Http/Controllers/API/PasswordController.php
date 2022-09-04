<?php

namespace App\Http\Controllers\API;
use App\Services\API\PasswordService;

use Illuminate\Http\Request;

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
}