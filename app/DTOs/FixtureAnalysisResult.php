<?php

namespace App\DTOs;

readonly class FixtureAnalysisResult
{
    public function __construct(
        public int   $fixtureId,
        public int   $tipsSaved,
        public bool  $skipped,
        public array $markets,
        public array $tipIds,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
