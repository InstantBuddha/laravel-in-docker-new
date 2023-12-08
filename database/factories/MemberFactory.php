<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        if(fake()->boolean(60)) {
            return [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone_number' => fake()->phoneNumber,
                'mailing_list' => fake()->boolean,
                'email_verified_at' => now(),
            ];
        }

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'zipcode' => fake()->postcode,
            'city' => fake()->city,
            'address' => fake()->address,
            'comment' => fake()->text,
            'mailing_list' => fake()->boolean,
            'email_verified_at' => now(),
        ];
    }
}
