<?php

namespace App\Experience\Domain;

use App\Experience\Domain\Exception\InvalidExperience;

final class Experience
{
    private function __construct(
        private string $id,
        private string $providerId,
        private string $title,
        private string $description,
    ) {}

    public static function create(
        string $id,
        string $providerId,
        string $title,
        string $description,
    ): self {
        if (trim($id) === '') {
            throw new InvalidExperience('Experience id cannot be empty.');
        }

        if (trim($providerId) === '') {
            throw new InvalidExperience('Provider id cannot be empty.');
        }

        if (trim($title) === '') {
            throw new InvalidExperience('Experience title cannot be empty.');
        }

        if (trim($description) === '') {
            throw new InvalidExperience('Experience description cannot be empty.');
        }

        return new self(
            $id,
            $providerId,
            $title,
            $description
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function providerId(): string
    {
        return $this->providerId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }
}
