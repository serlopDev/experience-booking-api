<?php

namespace Tests\Unit\Session\Domain;

use App\Session\Domain\Exception\InvalidSession;
use App\Session\Domain\Session;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function test_create_valid_session(): void
    {
        $now = new DateTimeImmutable('2030-01-01 10:00:00');

        $session = Session::create(
            id: '01JSESSION1234567890ABCDEFG',
            experienceId: '01JEXPERIENCE1234567890ABC',
            startsAt: new DateTimeImmutable('2030-01-02 18:00:00'),
            maxCapacity: 20,
            priceInCents: 2500,
            now: $now,
        );

        $this->assertSame(20, $session->maxCapacity());
        $this->assertSame(0, $session->reservedSeats());
        $this->assertSame(20, $session->availableSeats());
        $this->assertSame(2500, $session->priceInCents());
    }

    public function test_rejects_invalid_capacity(): void
    {
        $this->expectException(InvalidSession::class);

        Session::create(
            id: '01JSESSION1234567890ABCDEFG',
            experienceId: '01JEXPERIENCE1234567890ABC',
            startsAt: new DateTimeImmutable('2030-01-02 18:00:00'),
            maxCapacity: 0,
            priceInCents: 2500,
            now: new DateTimeImmutable('2030-01-01 10:00:00'),
        );
    }

    public function test_reserve_seats(): void
    {
        $now = new DateTimeImmutable('2030-01-01 10:00:00');

        $session = Session::create(
            id: '01JSESSION1234567890ABCDEFG',
            experienceId: '01JEXPERIENCE1234567890ABC',
            startsAt: new DateTimeImmutable('2030-01-02 18:00:00'),
            maxCapacity: 20,
            priceInCents: 2500,
            now: $now,
        );

        $session->reserveSeats(5, $now);

        $this->assertSame(5, $session->reservedSeats());
        $this->assertSame(15, $session->availableSeats());
    }

    public function test_rejects_reservation_over_capacity(): void
    {
        $this->expectException(InvalidSession::class);

        $now = new DateTimeImmutable('2030-01-01 10:00:00');

        $session = Session::create(
            id: '01JSESSION1234567890ABCDEFG',
            experienceId: '01JEXPERIENCE1234567890ABC',
            startsAt: new DateTimeImmutable('2030-01-02 18:00:00'),
            maxCapacity: 5,
            priceInCents: 2500,
            now: $now,
        );

        $session->reserveSeats(6, $now);
    }

    public function test_release_seats(): void
    {
        $now = new DateTimeImmutable('2030-01-01 10:00:00');

        $session = Session::create(
            id: '01JSESSION1234567890ABCDEFG',
            experienceId: '01JEXPERIENCE1234567890ABC',
            startsAt: new DateTimeImmutable('2030-01-02 18:00:00'),
            maxCapacity: 20,
            priceInCents: 2500,
            now: $now,
        );

        $session->reserveSeats(5, $now);
        $session->releaseSeats(2);

        $this->assertSame(3, $session->reservedSeats());
        $this->assertSame(17, $session->availableSeats());
    }
}
