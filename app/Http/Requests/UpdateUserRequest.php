<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('id'); // Captura o ID do usuário da URL

        return [
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId, 'user_id'), // Ignora o email do usuário sendo atualizado
            ],
            'password' => 'nullable|string|min:8',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
