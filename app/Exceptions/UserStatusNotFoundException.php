<?php

namespace App\Exceptions;

use Exception;

class UserStatusNotFoundException extends Exception
{
    // User Status Not Found Exception
    public function __contruct($message = 'User status not found')
    {
        parent::__contruct($message);
    }
}