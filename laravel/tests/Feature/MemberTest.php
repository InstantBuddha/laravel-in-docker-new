<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
//use Illuminate\Foundation\Testing\RefreshDatabase;    //This would reset the database, so if entries were present when testing, they would be lost.
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use DatabaseTransactions;   // creates the entries for testing, but removes them after that. The manually added entries will have a higher ID because auto increment will leave out the IDs of deleted test entries
    private const BASE_ENDPOINT = '/api/members/';

    public function testMember_index(): void
    {
        $members = Member::factory()->count(3)->create();
        $memberIds = $members->map(fn(Member $member) => $member->id)->toArray();
        $response = $this->get(self::BASE_ENDPOINT)->json('data');
        //$this->assertCount($members->count(), $response); //for empty database
        if (count($response) > 3) { //for a database with existing entries
            $response = array_slice($response, -3, null, true);
        }
        foreach ($response as $responseMember) {
            $this->assertContains($responseMember['id'], $memberIds);
        }
    }
    
    public function testMember_store(): void 
    {
        $member = Member::factory()->make();
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())->json('data');
        $this->assertNotNull($response['id']);
        $this->assertEquals($member->name, $response['name']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->email, $response['email']); // might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->phone_number, $response['phone_number']);// might be unnecessary or moved to a separate function to make the code more elegant
    }

    public function testMember_show(): void
    {
        $member = Member::factory()->count(3)->create()->random();
        $response = $this->get(self::BASE_ENDPOINT . $member->id)->json('data');
        $this->assertEquals($member->id, $response['id']); 
        $this->assertEquals($member->name, $response['name']);// might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->email, $response['email']);// might be unnecessary or moved to a separate function to make the code more elegant
        $this->assertEquals($member->phone_number, $response['phone_number']);// might be unnecessary or moved to a separate function to make the code more elegant
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
