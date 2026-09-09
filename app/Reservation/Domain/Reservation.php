<?php

namespace App\Reservation\Domain;

use App\Reservation\Domain\Exception\InvalidReservation;
use DateTimeImmutable;

final class Reservation
{
    private function __construct(
        private string $id,
        private string $sessionId,
        private string $userId,
        private string $contactEmail,
        private int $seats,
        private int $totalPriceInCents,
        private ReservationStatus $status,
    ) {}

    public static function create(
        string $id,
        string $sessionId,
        string $userId,
        string $contactEmail,
        int $seats,
        int $unitPriceInCents,
    ): self {
        if (trim($id) === '') {
            throw new InvalidReservation('Session id cannot be empty.');
        }

        if (trim($sessionId) === '') {
            throw new InvalidReservation('Session id cannot be empty.');
        }

        if (trim($userId) === '') {
            throw new InvalidReservation('User id cannot be empty.');
        }

        if (trim($contactEmail) === '') {
            throw new InvalidReservation('Contact email cannot be empty.');
        }

        if ($seats <= 0) {
            throw new InvalidReservation('Number of seats must be greater than zero.');
        }

        if ($unitPriceInCents < 0) {
            throw new InvalidReservation(
                'Unit price cannot be negative.'
            );
        }

        $totalPriceInCents = $unitPriceInCents * $seats;

        return new self(
            id: $id,
            sessionId: $sessionId,
            userId: $userId,
            contactEmail: $contactEmail,
            seats: $seats,
            totalPriceInCents: $totalPriceInCents,
            status: ReservationStatus::CONFIRMED,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function contactEmail(): string
    {
        return $this->contactEmail;
    }

    public function seats(): int
    {
        return $this->seats;
    }

    public function totalPriceInCents(): int
    {
        return $this->totalPriceInCents;
    }

    public function status(): ReservationStatus
    {
        return $this->status;
    }

    public function cancel(
        DateTimeImmutable $sessionStartsAt,
        DateTimeImmutable $now,
    ): void {
        if ($this->status === ReservationStatus::CANCELLED) {
            throw new InvalidReservation('Reservation is already cancelled.');
        }

        if ($now >= $sessionStartsAt->modify('-24 hours')) {
            throw new InvalidReservation('Cannot cancel a reservation within 24 hours of the session start time.');
        }

        $this->status = ReservationStatus::CANCELLED;
    }

    public static function reconstitute(
        string $id,
        string $sessionId,
        string $userId,
        string $contactEmail,
        int $seats,
        int $totalPriceInCents,
        ReservationStatus $status,
    ): self {
        return new self(
            id: $id,
            sessionId: $sessionId,
            userId: $userId,
            contactEmail: $contactEmail,
            seats: $seats,
            totalPriceInCents: $totalPriceInCents,
            status: $status,
        );
    }
}
