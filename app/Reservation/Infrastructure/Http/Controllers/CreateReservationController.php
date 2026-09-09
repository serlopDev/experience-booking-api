<?php

namespace App\Reservation\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Reservation\Application\CreateReservation\CreateReservation;
use App\Reservation\Infrastructure\Http\Requests\CreateReservationRequest;
use App\Reservation\Infrastructure\Http\Resources\ReservationResource;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CreateReservationController extends Controller
{
    public function __construct(
        private readonly CreateReservation $createReservation,
    ) {}

    public function __invoke(string $session, CreateReservationRequest $request)
    {
        $reservation = $this->createReservation->execute(
            id: (string) Str::ulid(),
            sessionId: $session,
            userId: $request->string('user_id')->toString(),
            contactEmail: $request->string('contact_email')->toString(),
            seats: $request->integer('seats'),
            now: new DateTimeImmutable,
        );

        return ReservationResource::make($reservation)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
