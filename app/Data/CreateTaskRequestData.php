<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;

class CreateTaskRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $title,
        #[Nullable, StringType]
        public ?string $description,
        #[Nullable, Date]
        public ?string $due_date,
        #[Nullable, Exists('priorities', 'id')]
        public ?int $priority_id,
        #[Nullable, Exists('categories', 'id')]
        public ?int $category_id,
    ) {}
}