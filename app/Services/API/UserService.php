<?php

namespace App\Services\API;

use DB;
use App\Models\User;
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
  public function __contruct(
    User $user,
  ) {
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

      $params['user_status_id'] = $status->id;
      // $user = $this->user->create($params);

      if (!($user instanceof User)) {
          throw new UserNotCreatedException;
      }

      $user->activationTokens()->save(new ActivationToken(['token' => $token]));

      $token = Hash::make(time() . uniqid());
      
      DB::commit();
    } catch (Exception $e) {
        DB::rollback();

        throw $e;
    }

    return $user;
        
  }
}