<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class TaskData extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public string $title,
        public ?string $description,
        public bool $is_completed,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?string $due_date,
        public ?int $priority_id,
        public ?int $category_id,
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        public \DateTimeInterface $created_at,
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        public \DateTimeInterface $updated_at,
        public ?CategoryData $category = null,
        public ?PriorityData $priority = null,
    ) {}
}