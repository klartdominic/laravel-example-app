<?php

namespace App\Services\API;

use DB;
use Hash;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\ActivationToken;
use App\Exceptions\UserStatusNotFoundException;

class UserService
{
  
  /**
   * @var App\Models\User
   */
  protected $user;

  /**
   * UserService Constructor
   */
  public function __construct(User $user)
  {
      $this->user = $user;
  }

  /**
   * Create a new user in db 
   * 
   * @param array $params
   * @return App\Models\User $user
   */
  public function createUser(array $params)
  {
    DB::beginTransaction();
    
    try
    {
      $status = UserStatus::where('name', config('user.statuses.pending'))
                            ->first();

      if(!($status instanceof UserStatus)){
        throw new UserStatusNotFoundException;
      } 

      $params['status_id'] = $status->id;
      // dd($params);
      
      $user = $this->user->create($params);

      if (!($user instanceof User)) {
          throw new UserNotCreatedException;
      }
      
      $token = Hash::make(time() . uniqid());

      $user->activationTokens()->save(new ActivationToken(['token' => $token]));
      
      DB::commit();
    } catch (Exception $e) {
        DB::rollback();

        throw $e;
    }

    return $user;
        
  }
}