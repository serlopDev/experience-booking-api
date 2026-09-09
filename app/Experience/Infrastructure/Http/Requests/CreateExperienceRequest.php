<?php

namespace App\Experience\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => [
                'required',
                'string',
                'max:255',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
        ];
    }
}
