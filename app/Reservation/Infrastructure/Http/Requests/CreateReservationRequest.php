<?php

namespace App\Reservation\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'string',
            ],
            'contact_email' => [
                'required',
                'email',
            ],
            'seats' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
