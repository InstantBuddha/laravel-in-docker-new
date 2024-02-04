<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_ENDPOINT = '/api/register-new-member';
    private const RATE_LIMIT = 60;

    public function test_rate_limit()
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        for ($i = 1; $i <= self::RATE_LIMIT; $i++) {
            $member = Member::factory()->make();
            $response = $this->postJson(self::BASE_ENDPOINT, $member->toArray(), $headers);
            $response->assertStatus(201);
            $response->assertHeader('X-Ratelimit-Limit', self::RATE_LIMIT);
            $response->assertHeader('X-Ratelimit-Remaining', self::RATE_LIMIT - $i);
        }

        $member = Member::factory()->make();
        $response = $this->postJson(self::BASE_ENDPOINT, $member->toArray(), $headers);
        $response->assertStatus(429);
    }
}