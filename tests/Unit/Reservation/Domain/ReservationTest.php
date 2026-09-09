<?php

namespace Tests\Unit\Reservation\Domain;

use App\Reservation\Domain\Exception\InvalidReservation;
use App\Reservation\Domain\Reservation;
use App\Reservation\Domain\ReservationStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ReservationTest extends TestCase
{
    public function test_create_valid_reservation(): void
    {
        $reservation = $this->createReservation();

        $this->assertSame(2, $reservation->seats());
        $this->assertSame(5000, $reservation->totalPriceInCents());
        $this->assertSame(
            ReservationStatus::CONFIRMED,
            $reservation->status(),
        );
    }

    public function test_rejects_zero_seats(): void
    {
        $this->expectException(InvalidReservation::class);

        Reservation::create(
            id: 'reservation-001',
            sessionId: 'session-001',
            userId: 'user-001',
            contactEmail: 'user@example.com',
            seats: 0,
            unitPriceInCents: 2500,
        );
    }

    public function test_rejects_negative_unit_price(): void
    {
        $this->expectException(InvalidReservation::class);

        Reservation::create(
            id: 'reservation-001',
            sessionId: 'session-001',
            userId: 'user-001',
            contactEmail: 'user@example.com',
            seats: 2,
            unitPriceInCents: -1,
        );
    }

    public function test_cancel_reservation(): void
    {
        $reservation = $this->createReservation();

        $reservation->cancel(
            sessionStartsAt: new DateTimeImmutable('2030-10-20 18:00:00'),
            now: new DateTimeImmutable('2030-10-18 18:00:00'),
        );

        $this->assertSame(
            ReservationStatus::CANCELLED,
            $reservation->status(),
        );
    }

    public function test_cannot_cancel_reservation_twice(): void
    {
        $reservation = $this->createReservation();

        $startsAt = new DateTimeImmutable('2030-10-20 18:00:00');
        $now = new DateTimeImmutable('2030-10-18 18:00:00');

        $reservation->cancel($startsAt, $now);

        $this->expectException(InvalidReservation::class);

        $reservation->cancel($startsAt, $now);
    }

    public function test_cannot_cancel_within_24_hours(): void
    {
        $reservation = $this->createReservation();

        $this->expectException(InvalidReservation::class);

        $reservation->cancel(
            sessionStartsAt: new DateTimeImmutable('2030-10-20 18:00:00'),
            now: new DateTimeImmutable('2030-10-20 10:00:00'),
        );
    }

    public function test_cannot_cancel_exactly_24_hours_before(): void
    {
        $reservation = $this->createReservation();

        $this->expectException(InvalidReservation::class);

        $reservation->cancel(
            sessionStartsAt: new DateTimeImmutable('2030-10-20 18:00:00'),
            now: new DateTimeImmutable('2030-10-19 18:00:00'),
        );
    }

    private function createReservation(): Reservation
    {
        return Reservation::create(
            id: 'reservation-001',
            sessionId: 'session-001',
            userId: 'user-001',
            contactEmail: 'user@example.com',
            seats: 2,
            unitPriceInCents: 2500,
        );
    }
}
