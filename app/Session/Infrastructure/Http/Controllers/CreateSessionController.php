<?php

namespace App\Session\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Session\Application\CreateSession\CreateSession;
use App\Session\Infrastructure\Http\Requests\CreateSessionRequest;
use App\Session\Infrastructure\Http\Resources\SessionResource;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CreateSessionController extends Controller
{
    public function __construct(
        private readonly CreateSession $createSession,
    ) {}

    public function __invoke(string $experience, CreateSessionRequest $request)
    {
        $session = $this->createSession->execute(
            id: (string) Str::ulid(),
            experienceId: $experience,
            startsAt: new \DateTimeImmutable($request->string('starts_at')->toString()),
            maxCapacity: $request->integer('max_capacity'),
            priceInCents: $request->integer('price_in_cents'),
            now: new \DateTimeImmutable,
        );

        return SessionResource::make($session)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
