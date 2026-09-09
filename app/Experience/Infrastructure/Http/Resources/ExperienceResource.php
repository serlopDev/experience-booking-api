<?php

namespace App\Experience\Infrastructure\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id(),
            'provider_id' => $this->providerId(),
            'title' => $this->title(),
            'description' => $this->description(),
        ];
    }
}
