<?php

namespace Tests\Feature\Reservation\Infrastructure\Http;

use App\Experience\Infrastructure\Persistence\Eloquent\ExperienceModel;
use App\Reservation\Infrastructure\Persistence\Eloquent\ReservationModel;
use App\Session\Infrastructure\Persistence\Eloquent\SessionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CancelReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_reservation(): void
    {
        [$session, $reservation] = $this->createReservation(
            startsAt: '2030-10-20 18:00:00',
            reservedSeats: 2,
            status: 'confirmed',
        );

        $response = $this->deleteJson(
            route('reservations.destroy', [
                'reservation' => $reservation->id,
            ]),
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $reservation->id)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 0,
        ]);
    }

    public function test_rejects_cancelling_reservation_twice(): void
    {
        [$session, $reservation] = $this->createReservation(
            startsAt: '2030-10-20 18:00:00',
            reservedSeats: 0,
            status: 'cancelled',
        );

        $response = $this->deleteJson(
            route('reservations.destroy', [
                'reservation' => $reservation->id,
            ]),
        );

        $response->assertUnprocessable();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 0,
        ]);
    }

    public function test_rejects_cancellation_within_24_hours(): void
    {
        [$session, $reservation] = $this->createReservation(
            startsAt: now()->addHours(12)->format('Y-m-d H:i:s'),
            reservedSeats: 2,
            status: 'confirmed',
        );

        $response = $this->deleteJson(
            route('reservations.destroy', [
                'reservation' => $reservation->id,
            ]),
        );

        $response->assertUnprocessable();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'reserved_seats' => 2,
        ]);
    }

    public function test_rejects_non_existing_reservation(): void
    {
        $response = $this->deleteJson(
            route('reservations.destroy', [
                'reservation' => (string) Str::ulid(),
            ]),
        );

        $response->assertNotFound();
    }

    private function createReservation(
        string $startsAt,
        int $reservedSeats,
        string $status,
    ): array {
        $experience = ExperienceModel::query()->create([
            'id' => (string) Str::ulid(),
            'provider_id' => 'provider-001',
            'title' => 'Madrid walking tour',
            'description' => 'Walking tour through Madrid.',
        ]);

        $session = SessionModel::query()->create([
            'id' => (string) Str::ulid(),
            'experience_id' => $experience->id,
            'starts_at' => $startsAt,
            'max_capacity' => 20,
            'reserved_seats' => $reservedSeats,
            'price_in_cents' => 2500,
        ]);

        $reservation = ReservationModel::query()->create([
            'id' => (string) Str::ulid(),
            'session_id' => $session->id,
            'user_id' => 'user-001',
            'contact_email' => 'user@example.com',
            'seats' => 2,
            'total_price_in_cents' => 5000,
            'status' => $status,
        ]);

        return [$session, $reservation];
    }
}
