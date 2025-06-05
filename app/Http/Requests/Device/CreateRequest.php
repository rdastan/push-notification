<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'device_token' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'device_token.required' => 'Поле device_token обязателен для заполнения',
            'device_token.string' => 'device_token должна быть строкой',
            'user_id.required' => 'Поле user_id обязателен для заполнения',
            'user_id.exists' => 'Пользователь не найден',
        ];
    }
}
