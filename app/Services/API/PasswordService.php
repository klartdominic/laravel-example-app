<?php

namespace App\Services\API;

use DB;
use Hash;
use Mail;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\ActivationToken;
use App\Models\PasswordReset;
use App\Exceptions\UserStatusNotFoundException;
use InvalidArgumentException;
use App\Services\API\UserService;
use App\Mail\ForgotPasswordMail;
use App\Mail\PasswordChange;
use App\Exceptions\InvalidPasswordResetTokenException;

class PasswordService
{
  
  /**
   * @var App\Models\User
   */
  protected $user;

  /**
   * @var App\Services\API\UserService
   */
  protected $userService;

  /**
   * @var App\Models\passwordReset $passwordReset
   */
  protected $passwordReset;

  /**
   * PasswordService Constructor
   */
  public function __construct(
    User $user, 
    UserService $userService, 
    PasswordReset $passwordReset
  )
  {
      $this->user = $user;
      $this->userService = $userService;
      $this->passwordReset = $passwordReset;
  }

  /**
   * Handles the Forgot Password request of the user
   * 
   * @param array $params
   * @return PasswordReset
   */
  public function forgotPassword(string $email)
  {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      throw new InvalidArgumentException('Invalid email address.');
    }   

    // check if user exists
    $user = $this->userService->findByEmail($email);

    // generate new token
    $token = $this->passwordReset
            ->create([
              'email' => $email,
              'token' => Hash::make(uniqid() . time()),
            ]);
            
    $token->user = $user;

    // send password reset link email notification to user
    Mail::to($user->email)->send(new ForgotPasswordMail($token));

    return $token;
  }

  /**
     * Handles the Reset Password request of the User
     *
     * @param array $data
     * @return PasswordReset
     */
    public function reset(array $data)
    {
        if (!array_key_exists('token', $data)) {
            throw new InvalidArgumentException('Missing required token field.');
        }

        if (!array_key_exists('password', $data)) {
            throw new InvalidArgumentException('Missing required password field.');
        }

        // validate if token is valid
        $token = $this->passwordReset
                    ->where('token', $data['token'])
                    ->first();
                    
        if (!($token instanceof PasswordReset)) {
            throw new InvalidPasswordResetTokenException;
        }

        // get active user status
        $status = UserStatus::where('name', config('user.statuses.active'))->first();

        if (!($status instanceof UserStatus)) {
            throw new RuntimeException('Unable to retrieve user status');
        }

        // retrieve user to fetch new password
        $user = $this->userService->findByEmail($token->email);

        // update user password
        $user->update([
            'password' => Hash::make($data['password']),
            'login_attempts' => 0, // reset failed attempts
        ]);

        // revoke the token
        $token->delete();

        // send successful password reset email notification to user
        Mail::to($user)->send(new PasswordChange($user));

        // return user
        return $user;
    }
}