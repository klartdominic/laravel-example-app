<?php

namespace App\Http\Controllers\API;

use App\Services\API\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\API\CreateUserRequest;
use App\Http\Requests\API\RegistrationRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserProfileResource;
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
            $formData = $request->validated();
            $user = $this->userService->createUser($formData);
            $this->response['data'] = new UserResource($user);
        } catch (Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        } 

        return response()->json($this->response, $this->response['code']);
    } 

    /**
     * Updates user information
     *
     * @param App\Http\Requests\CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {
        $request->validated();

        try {
            $formData = [
                'id' => $request->getUserId(),
                'name' => $request->getName(),
                'email_address' => $request->getEmailAddress(),
                'address' => $request->getAddress(),
                'phone_number' => $request->getPhoneNumber(),
                'birthday' => $request->getBirthday(),
            ];
            // $formData = $request->validated();
            
            $user = $this->userService->update($formData);
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