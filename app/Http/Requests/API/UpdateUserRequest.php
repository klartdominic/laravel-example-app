<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'name' => 'required',
            'email_address' => 'required',
            'address' => 'required',
            'birthday' => 'required',
            'phone_number' => 'required',
        ];
    }
    
    public function getUserId()
    {
        return $this->input('id', null);
    }

    public function getName()
    {
        return $this->input('name', null);
    }

    public function getEmailAddress()
    {
        return $this->input('email_address', null);
    }

    public function getAddress()
    {
        return $this->input('address', null);
    }

    public function getPhoneNumber()
    {
        return $this->input('phone_number', null);
    }

    public function getBirthday()
    {
        return $this->input('birthday', null);
    }
}