<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    /**
     * @var bool 
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array
     */
    protected $casts =[
        'revoked' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = ['email', 'token'];
}