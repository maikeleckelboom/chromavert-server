<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => 'sometimes|min:2|max:255',
            'email' => 'sometimes|unique:users,email,' . $id,
            'password' => 'sometimes|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name must not be greater than 255 characters.',
            'email.unique' => 'Email already exists.',
            'password.confirmed' => 'Password does not match.',
            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, png, jpg, gif, svg.',
            'avatar.max' => 'Avatar may not be greater than 2048 kilobytes.'
        ];
    }
}
