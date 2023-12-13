<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_ENDPOINT = '/api/members/';
    private const RATE_LIMIT = 60;

    public function test_rate_limit()
    {
        for ($i = 1; $i <= self::RATE_LIMIT; $i++) {
            $member = Member::factory()->make();
            $this->withMiddleware(['api']);
            $response = $this->post(self::BASE_ENDPOINT, $member->toArray())
                ->assertCreated()
                ->assertHeader('X-Ratelimit-Limit', self::RATE_LIMIT)
                ->assertHeader('X-Ratelimit-Remaining', self::RATE_LIMIT - $i);
        }

        $member = Member::factory()->make();
        $this->withMiddleware(['api']);
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())
            ->assertStatus(429);
    }

}
