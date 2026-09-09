<?php

namespace App\Session\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'starts_at' => [
                'required',
                'date',
            ],
            'max_capacity' => [
                'required',
                'integer',
                'min:1',
            ],
            'price_in_cents' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
