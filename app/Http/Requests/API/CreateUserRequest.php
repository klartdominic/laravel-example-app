<?php

namespace App\Http\Requests\API;

use App\Rules\Password;
use App\Rules\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email_address' => ['required', new EmailAddress, 'unique:users,email_address'],
            'password' => ['required', new Password]
        ];
    }

    public function getName()
    {
        return $this->input('name', null);
    }

    public function getEmailAddress()
    {
        return $this->input('email_address', null);
    }

    public function getPassword2()
    {
        return $this->input('password', null);
    }
}