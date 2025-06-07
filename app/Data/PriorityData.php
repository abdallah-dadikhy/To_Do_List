<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PriorityData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $level,
    ) {}
}