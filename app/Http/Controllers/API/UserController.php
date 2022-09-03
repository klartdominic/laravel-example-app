<?php

namespace App\Http\Controllers\API;

use App\Services\API\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\API\CreateUserRequest;
use App\Http\Requests\API\RegistrationRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    /**
     * @var App\Services\API\UserService
     */
    protected $userService;

    /**
     * UserController constructor
     */
    public function __construct(
        UserService $userService,
    ){
        parent::__construct();
        
        $this->userService = $userService;
    }

    public function index()
    {
        return User::all();
    }

    public function create(RegistrationRequest $request) 
    {   

        try {
            // $formData = [
            //     'name' => $request->getName(),
            //     'email_address' => $request->getEmailAddress(),
            //     'password' => $request->getInputPassword()
            // ];
            // $formData = $request->validate([
            //     'name' => 'required|max:255',
            //     'email_address' => 'required',
            //     'password' => 'required',
            // ]);
            $formData = $request->validated();
            $user = $this->userService->createUser($formData);
            $this->response['data'] = new UserResource($user);
        } catch (Exception $e) { // @codeCoverageIgnoreStart
            $this->response = [
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        } // @codeCoverageIgnoreEnd

        return response()->json($this->response, $this->response['code']);
    } 
}