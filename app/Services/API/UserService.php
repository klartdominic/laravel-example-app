<?php

namespace App\Services\API;

use DB;
use Hash;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\ActivationToken;
use App\Models\UserProfile;
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
   * @var App\Models\UserProfile
   * 
   */
  protected $userProfile;

  /**
   * UserService Constructor
   */
  public function __construct(
    User $user,
    UserProfile $userProfile,
  )
  {
      $this->user = $user;
      $this->userProfile = $userProfile;
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

  /**
   * Retrieves a profile from user by id
   * 
   * @param int $id
   * @return UserProfile $userProfile
   */
  public function findProfileById(int $id){
    $user = $this->user->find($id);

    if (!($user instanceof User)) {
      throw new UserNotFoundException;
    }

    $userPRofile = $this->userProfile
                    ->where('user_id', $user->id)
                    ->first();
                    
    return $userPRofile;
  }

  /**
   * Updates user in the database
   *
   * @param array $params
   * @return App\Models\User $user
   */
  public function update(array $params)
  {
    DB::beginTransaction();
    try {
      
      // retrieve user information
      $user = $this->findById($params['id']);
      $profile = $this->findProfileById($params['id']);
    
      if (array_key_exists('password', $params)) {
        // update user password if provided in request or retain the current password
        $params['password'] = strlen($params['password']) > 0 ?
        Hash::make($params['password']) :
        $user->password;
      }
      
      // perform update)
      $user->update($params);
      // $update = $userProfile->update($params);
      if(!$profile){
        $params['user_id'] = $user->id;
        $profile = $this->userProfile->create($params);
      } else {
        $profile->update($params);
      }
      DB::commit();
      
    } catch (Exception $e) {
      DB::rollback();
      throw $e;
      
    } 
    // dd($profile);
    
    return $user; 
  }

    
}    