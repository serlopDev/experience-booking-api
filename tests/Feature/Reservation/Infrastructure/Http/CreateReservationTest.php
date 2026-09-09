<?php

namespace Tests\Feature\Reservation\Infrastructure\Http;

use App\Experience\Infrastructure\Persistence\Eloquent\ExperienceModel;
use App\Session\Infrastructure\Persistence\Eloquent\SessionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CreateReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_reservation(): void
    {
        $session = $this->createSession(
            maxCapacity: 20,
            reservedSeats: 0,
            priceInCents: 2500,
        );

        $response = $this->postJson(
            route('sessions.reservations.store', [
                'session' => $session->id,
            ]),
            [
                'user_id' => 'user-001',
                'contact_email' => 'user@example.com',
                'seats' => 2,
            ],
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.session_id', $session->id)
            ->assertJsonPath('data.user_id', 'user-001')
            ->assertJsonPath('data.contact_email', 'user@example.com')
            ->assertJsonPath('data.seats', 2)
            ->assertJsonPath('data.total_price_in_cents', 5000)
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('reservations', [
            'session_id' => $session->id,
            'user_id' => 'user-001',
            'seats' => 2,
            'total_price_in_cents' => 5000,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 2,
        ]);
    }

    public function test_rejects_reservation_when_there_are_not_enough_seats(): void
    {
        $session = $this->createSession(
            maxCapacity: 2,
            reservedSeats: 0,
            priceInCents: 2500,
        );

        $response = $this->postJson(
            route('sessions.reservations.store', [
                'session' => $session->id,
            ]),
            [
                'user_id' => 'user-001',
                'contact_email' => 'user@example.com',
                'seats' => 3,
            ],
        );

        $response->assertUnprocessable();

        $this->assertDatabaseCount('reservations', 0);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 0,
        ]);
    }

    public function test_rejects_reservation_for_non_existing_session(): void
    {
        $sessionId = '01JNOTFOUND1234567890ABCDE';

        $response = $this->postJson(
            route('sessions.reservations.store', [
                'session' => $sessionId,
            ]),
            [
                'user_id' => 'user-001',
                'contact_email' => 'user@example.com',
                'seats' => 2,
            ],
        );

        $response->assertNotFound();

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_rejects_reservation_for_started_session(): void
    {
        $session = $this->createSession(
            startsAt: '2020-10-15 18:00:00',
        );

        $response = $this->postJson(
            route('sessions.reservations.store', [
                'session' => $session->id,
            ]),
            [
                'user_id' => 'user-001',
                'contact_email' => 'user@example.com',
                'seats' => 2,
            ],
        );

        $response->assertUnprocessable();

        $this->assertDatabaseCount('reservations', 0);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 0,
        ]);
    }

    public function test_validates_reservation_request(): void
    {
        $session = $this->createSession();

        $response = $this->postJson(
            route('sessions.reservations.store', [
                'session' => $session->id,
            ]),
            [
                'user_id' => '',
                'contact_email' => 'invalid-email',
                'seats' => 0,
            ],
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'user_id',
                'contact_email',
                'seats',
            ]);

        $this->assertDatabaseCount('reservations', 0);
    }

    private function createSession(
        int $maxCapacity = 20,
        int $reservedSeats = 0,
        int $priceInCents = 2500,
        string $startsAt = '2030-10-15 18:00:00',
    ): SessionModel {
        $experience = ExperienceModel::query()->create([
            'id' => (string) Str::ulid(),
            'provider_id' => 'provider-001',
            'title' => 'Madrid walking tour',
            'description' => 'Walking tour through Madrid.',
        ]);

        return SessionModel::query()->create([
            'id' => (string) Str::ulid(),
            'experience_id' => $experience->id,
            'starts_at' => $startsAt,
            'max_capacity' => $maxCapacity,
            'reserved_seats' => $reservedSeats,
            'price_in_cents' => $priceInCents,
        ]);
    }
}
