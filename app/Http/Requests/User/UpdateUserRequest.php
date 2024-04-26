<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'name' => ['sometimes', 'string', "min:2", 'max:255'],
            'password' => ['sometimes', 'string', Password::defaults()],
            'username' => ['sometimes', 'nullable', 'string', 'min:2', 'max:255'],
            'avatar' => ['sometimes', 'nullable', 'url', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
