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
        $memberProperties = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'is_subscribed_to_mailing_list' => fake()->boolean,
            'email_verified_at' => now(),
        ];

        if (fake()->boolean(60)) {
            return array_merge(
                $memberProperties,
                [
                    'zipcode' => fake()->postcode,
                    'city' => fake()->city,
                    'address' => fake()->address,
                    'comment' => fake()->text,
                ]
            );
        }

        return $memberProperties;
    }
}
