<?php

namespace App\Experience\Infrastructure\Http\Controllers;

use App\Experience\Application\CreateExperience\CreateExperience;
use App\Experience\Infrastructure\Http\Requests\CreateExperienceRequest;
use App\Experience\Infrastructure\Http\Resources\ExperienceResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CreateExperienceController extends Controller
{
    public function __construct(
        private readonly CreateExperience $createExperience,
    ) {}

    public function __invoke(CreateExperienceRequest $request)
    {
        $experience = $this->createExperience->execute(
            id: (string) Str::ulid(),
            providerId: $request->string('provider_id')->toString(),
            title: $request->string('title')->toString(),
            description: $request->string('description')->toString(),
        );

        return ExperienceResource::make($experience)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
