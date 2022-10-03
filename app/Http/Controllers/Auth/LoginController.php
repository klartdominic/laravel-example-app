<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\API\UserService;
use Carbon\Carbon;
use DateTime;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $userService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request){

        $credentials = $request->only('email','password');
        $result = ['status'=>200];

        if(Auth::attempt(['email'=> $credentials['email'], 'password'=> $credentials['password'], 'status_id'=> 1])){
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->save();
            $result['token_type'] = 'Bearer';
            $result['expires_in'] = $tokenResult->token->expires_at;
            $result['access_token'] = $tokenResult->accessToken;
            $this->userService->successfulLogin($request->get('email'));
        }
        else if(Auth::attempt(['email'=> $credentials['email'], 'password'=> $credentials['password'], 'status_id'=> 6])){
            $user = $request->user();
            $dtNow = Carbon::parse($user->updated_at);
            $dtToCompare = Carbon::now();
            $diff = $dtNow->diffInSeconds($dtToCompare);
            if($diff > 600){
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->save();
                $result['token_type'] = 'Bearer';
                $result['expires_in'] = $tokenResult->token->expires_at;
                $result['access_token'] = $tokenResult->accessToken;
                $this->userService->successfulLogin($request->get('email'));
            }
            else{
                $result = [
                    'status' => 401,
                    'error' => 'The account is now locked. Please wait for 10 mins or reset your password in the forgot password section.'
                ];
            }   
        }
        else{
            $result = $this->userService->loginAttempt($request->get('email'));
        }
        return response()->json($result, $result['status']);
    }
}