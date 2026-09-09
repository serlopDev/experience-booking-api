<?php

namespace Tests\Unit\Experience\Domain;

use App\Experience\Domain\Exception\InvalidExperience;
use App\Experience\Domain\Experience;
use PHPUnit\Framework\TestCase;

class ExperienceTest extends TestCase
{
    /**
     * Create a new experience with valid data and assert that it is created successfully.
     */
    public function test_create_valid_experience(): void
    {
        $data = [
            'id' => '01JABCDE1234567890ABCDEFGH',
            'providerId' => 'provider-001',
            'title' => 'Madrid walking tour',
            'description' => 'Walking tour through Madrid.',
        ];

        $experience = Experience::create(
            id: $data['id'],
            providerId: $data['providerId'],
            title: $data['title'],
            description: $data['description'],
        );

        $this->assertSame($data['id'], $experience->id());
        $this->assertSame($data['providerId'], $experience->providerId());
        $this->assertSame($data['title'], $experience->title());
        $this->assertSame($data['description'], $experience->description());
    }

    public function test_create_experience_with_empty_id(): void
    {
        $this->expectException(InvalidExperience::class);
        $this->expectExceptionMessage('Experience id cannot be empty.');

        Experience::create(
            id: '',
            providerId: 'provider-001',
            title: 'Madrid walking tour',
            description: 'Walking tour through Madrid.',
        );
    }

    public function test_create_experience_with_empty_provider_id(): void
    {
        $this->expectException(InvalidExperience::class);
        $this->expectExceptionMessage('Provider id cannot be empty.');

        Experience::create(
            id: '01JABCDE1234567890ABCDEFGH',
            providerId: '',
            title: 'Madrid walking tour',
            description: 'Walking tour through Madrid.',
        );
    }

    public function test_create_experience_with_empty_title(): void
    {
        $this->expectException(InvalidExperience::class);
        $this->expectExceptionMessage('Experience title cannot be empty.');

        Experience::create(
            id: '01JABCDE1234567890ABCDEFGH',
            providerId: 'provider-001',
            title: '',
            description: 'Walking tour through Madrid.',
        );
    }

    public function test_create_experience_with_empty_description(): void
    {
        $this->expectException(InvalidExperience::class);
        $this->expectExceptionMessage('Experience description cannot be empty.');

        Experience::create(
            id: '01JABCDE1234567890ABCDEFGH',
            providerId: 'provider-001',
            title: 'Madrid walking tour',
            description: '',
        );
    }
}
