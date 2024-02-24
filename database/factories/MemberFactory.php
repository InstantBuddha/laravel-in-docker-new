<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'is_subscribed_to_mailing_list' => fake()->boolean,
            'email_verified_at' => now(),
            'zipcode' => fake()->optional()->postcode,
            'city' => fake()->optional()->city,
            'address' => fake()->optional()->address,
            'comment' => fake()->optional()->text,
        ];
    }
}
