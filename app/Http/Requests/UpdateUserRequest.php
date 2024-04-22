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
        return [
            'name' => 'sometimes|min:2|max:255',
            'email' => 'sometimes|unique:users,email,' . $this->route('id'),
            'password' => 'sometimes|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
