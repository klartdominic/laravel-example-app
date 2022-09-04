<?php

namespace App\Services\API;

use DB;
use Hash;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\ActivationToken;
use App\Exceptions\UserStatusNotFoundException;
use InvalidArgumentException;
use App\Services\API\UserService;

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
   * PasswordService Constructor
   */
  public function __construct(User $user, UserService $userService)
  {
      $this->user = $user;
      $this->userService = $userService;
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
}