<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'slot_id' => ['required', 'integer', 'exists:slots,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Заполните это поле.',
            'email' => 'Введите корректный email.',
            'max' => 'Не длиннее :max символов.',
            'slot_id.integer' => 'Такого слота нет.',
            'slot_id.exists' => 'Такого слота нет.',
        ];
    }
}
