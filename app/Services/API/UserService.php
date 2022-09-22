<?php

namespace App\Services\API;

use DB;
use Hash;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\ActivationToken;
use App\Exceptions\UserStatusNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotCreatedException;

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

      $params['password'] = Hash::make($params['password']);
      $status = UserStatus::where('name', config('user.statuses.pending'))
                            ->first();

      if(!($status instanceof UserStatus)){
        throw new UserStatusNotFoundException;
      } 

      $params['status_id'] = $status->id;
      
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

  /**
   * Handle finding the User by email
   * 
   * @param string $email
   * @return User $user
   */
  public function findByEmail($email)
  {
    //retrieve user
    $user = $this->user
            ->where('email', $email)
            ->first();

    // check if user exists
    if (!($user instanceof User))
    {
      throw new UserNotFoundException;
    }

    return $user;
    
  }

  /**
     * Retrieves a user by id
     *
     * @param int $id
     * @return User $user
     */
    public function findById(int $id)
    {
        // retrieve the user
        $user = $this->user->find($id);

        if (!($user instanceof User)) {
          throw new UserNotFoundException;  
        }

        return $user;
    }
}    