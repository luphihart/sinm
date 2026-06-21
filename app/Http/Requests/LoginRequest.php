<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'login_identifier.required' => 'NIS, NISN, atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
