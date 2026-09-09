<?php

namespace App\Session\Domain;

use App\Session\Domain\Exception\InvalidSession;
use DateTimeImmutable;

final class Session
{
    private function __construct(
        private string $id,
        private string $experienceId,
        private DateTimeImmutable $startsAt,
        private int $maxCapacity,
        private int $reservedSeats,
        private int $priceInCents
    ) {}

    public static function create(
        string $id,
        string $experienceId,
        DateTimeImmutable $startsAt,
        int $maxCapacity,
        int $priceInCents,
        DateTimeImmutable $now,
    ): self {
        if (trim($id) === '') {
            throw new InvalidSession('Session id cannot be empty.');
        }

        if (trim($experienceId) === '') {
            throw new InvalidSession('Experience id cannot be empty.');
        }

        if ($maxCapacity <= 0) {
            throw new InvalidSession('Max capacity must be greater than zero.');
        }

        if ($priceInCents < 0) {
            throw new InvalidSession('Price cannot be negative.');
        }

        if ($startsAt <= $now) {
            throw new InvalidSession('Session must start in the future.');
        }

        return new self(
            $id,
            $experienceId,
            $startsAt,
            $maxCapacity,
            0,
            $priceInCents,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function experienceId(): string
    {
        return $this->experienceId;
    }

    public function startsAt(): DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function maxCapacity(): int
    {
        return $this->maxCapacity;
    }

    public function reservedSeats(): int
    {
        return $this->reservedSeats;
    }

    public function priceInCents(): int
    {
        return $this->priceInCents;
    }

    public function availableSeats(): int
    {
        return $this->maxCapacity - $this->reservedSeats;
    }

    public function reserveSeats(int $seats, DateTimeImmutable $now): void
    {
        if ($seats <= 0) {
            throw new InvalidSession(
                'Number of seats to reserve must be greater than zero.'
            );
        }

        if ($this->startsAt <= $now) {
            throw new InvalidSession(
                'Cannot reserve seats for a session that has already started.'
            );
        }

        if ($seats > $this->availableSeats()) {
            throw new InvalidSession(
                'Not enough available seats to reserve.'
            );
        }

        $this->reservedSeats += $seats;
    }

    public function releaseSeats(int $seats): void
    {

        if ($seats <= 0) {
            throw new InvalidSession('Number of seats to release must be greater than zero.');
        }

        if ($seats > $this->reservedSeats) {
            throw new InvalidSession('Cannot release more seats than are currently reserved.');
        }

        $this->reservedSeats -= $seats;
    }

    public static function reconstitute(
        string $id,
        string $experienceId,
        DateTimeImmutable $startsAt,
        int $maxCapacity,
        int $reservedSeats,
        int $priceInCents,
    ): self {
        return new self(
            $id,
            $experienceId,
            $startsAt,
            $maxCapacity,
            $reservedSeats,
            $priceInCents,
        );
    }
}
