<?php

namespace Tests\Feature\Session\Infrastructure\Http;

use App\Experience\Infrastructure\Persistence\Eloquent\ExperienceModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_session(): void
    {
        $experience = ExperienceModel::query()->create([
            'id' => '01JABCDE1234567890ABCDEFGH',
            'provider_id' => 'provider-001',
            'title' => 'Madrid walking tour',
            'description' => 'Walking tour through Madrid.',
        ]);

        $data = [
            'starts_at' => '2030-10-15T18:00:00+02:00',
            'max_capacity' => 20,
            'price_in_cents' => 2500,
        ];

        $response = $this->postJson(
            route('experiences.sessions.store', [
                'experience' => $experience->id,
            ]),
            $data,
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.experience_id', $experience->id)
            ->assertJsonPath('data.starts_at', $data['starts_at'])
            ->assertJsonPath('data.max_capacity', 20)
            ->assertJsonPath('data.reserved_seats', 0)
            ->assertJsonPath('data.available_seats', 20)
            ->assertJsonPath('data.price_in_cents', 2500);

        $this->assertDatabaseHas('sessions', [
            'experience_id' => $experience->id,
            'max_capacity' => 20,
            'reserved_seats' => 0,
            'price_in_cents' => 2500,
        ]);
    }

    public function test_create_session_with_non_existing_experience(): void
    {
        $experienceId = '9999999999999999999999999';

        $response = $this->postJson(
            route('experiences.sessions.store', [
                'experience' => $experienceId,
            ]),
            [
                'starts_at' => '2030-10-15T18:00:00+02:00',
                'max_capacity' => 20,
                'price_in_cents' => 2500,
            ],
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'message',
                "Experience with id {$experienceId} not found."
            );

        $this->assertDatabaseCount('sessions', 0);
    }
}
