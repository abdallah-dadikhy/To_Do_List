<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Illuminate\Validation\Rule;

class UpdateUserRequestData extends Data
{
    public function __construct(
        #[Sometimes, StringType, Max(255)]
        public ?string $name,
        #[Sometimes, StringType, Email, Max(255)]
        public ?string $email,
        #[Nullable, StringType, Min(8)]
        public ?string $password,
        #[Sometimes, StringType]
        public ?string $role,
    ) {}

    public static function rules(): array
    {
        // Accessing the ID from the route for unique rule
        $userId = request()->route('user'); // Or use $this->route('user') in a Form Request

        return [
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'role' => ['sometimes', 'string', Rule::in(['owner', 'guest'])],
        ];
    }
}