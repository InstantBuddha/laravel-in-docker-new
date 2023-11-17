<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;
    private const BASE_ENDPOINT = '/api/members/';

    public function testMember_index(): void
    {
        $members = Member::factory()->count(3)->create();
        $memberIds = $members->map(fn(Member $member) => $member->id)->toArray();
        $response = $this->get(self::BASE_ENDPOINT)->json('data');
        $this->assertCount($members->count(), $response);

        foreach ($response as $responseMember) {
            $this->assertContains($responseMember['id'], $memberIds);
        }
    }

    public function testMember_store(): void
    {
        $member = Member::factory()->make();    //a create tárolná is : eloquent működésére utánanézni ehhez: pont jó ez a teszt, itt pont rossz lenne, ha ccreate lenne
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())->json('data');
        $this->assertNotNull($response['id']);  //legyen ez is inkább equals
        $this->assertEquals($member->name, $response['name']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->email, $response['email']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->phone_number, $response['phone_number']); // might be unnecessary or moved to a separate function to make the code more elegant
    }   //teszteknél nem baj ha van ismétlődés

    public function testMember_show(): void
    {
        $member = Member::factory()->count(3)->create()->random();
        $response = $this->get(self::BASE_ENDPOINT . $member->id)->json('data');
        $this->assertEquals($member->id, $response['id']);
        $this->assertEquals($member->name, $response['name']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->email, $response['email']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->phone_number, $response['phone_number']); // might be unnecessary or moved to a separate function to make the code more elegant
    }


    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
