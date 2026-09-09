<?php

namespace Tests\Feature\Experience\Infrastructure\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateExperienceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of an experience via the API and assert that it is created successfully.
     */
    public function test_create_experience(): void
    {
        $data = [
            'provider_id' => fake()->uuid(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];

        $response = $this->postJson(route('experiences.store'), $data);

        $response->assertCreated()
            ->assertJsonPath('data.provider_id', $data['provider_id'])
            ->assertJsonPath('data.title', $data['title'])
            ->assertJsonPath('data.description', $data['description']);

        $this->assertDatabaseHas('experiences', [
            'provider_id' => $data['provider_id'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Test rejection of experience creation when title is empty.
     */
    public function test_create_experience_with_empty_title(): void
    {
        $data = [
            'provider_id' => fake()->uuid(),
            'title' => '',
            'description' => fake()->paragraph(),
        ];

        $response = $this->postJson(route('experiences.store'), $data);

        $response->assertunprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseCount('experiences', 0);
    }
}
