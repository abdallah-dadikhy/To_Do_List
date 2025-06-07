<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Min;
use Illuminate\Validation\Rule; // لاستخدام Rule::in

class CreateUserRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,
        #[Required, StringType, Email, Max(255), Unique('users', 'email')]
        public string $email,
        #[Required, StringType, Min(8)]
        public string $password,
        #[Required, StringType]
        public string $role,
    ) {}

    public static function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in(['owner', 'guest'])],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
        ];
    }
}