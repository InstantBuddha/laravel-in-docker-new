<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\WelcomeEmail;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
        $member = Member::factory()->make();
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())->json('data');
        $this->assertNotNull($response['id']);  //this cannot be changed to assertEquals as $member->id is null
        $this->assertEquals($member->name, $response['name']); 
        $this->assertEquals($member->email, $response['email']); 
        $this->assertEquals($member->phone_number, $response['phone_number']); 
    }   

    public function testMember_show(): void
    {
        $member = Member::factory()->count(3)->create()->random();
        $response = $this->get(self::BASE_ENDPOINT . $member->id)->json('data');
        $this->assertEquals($member->id, $response['id']);
        $this->assertEquals($member->name, $response['name']); 
        $this->assertEquals($member->email, $response['email']); 
        $this->assertEquals($member->phone_number, $response['phone_number']); 
    }

    public function test_mailable_content(): void
    {
        $member = Member::factory()->create();

        $mailable = new WelcomeEmail($member);

        Mail::fake();

        Mail::to($member->email)->send($mailable);

        Mail::assertSent(WelcomeEmail::class, function (WelcomeEmail $mail) use ($member){
            return $mail->hasTo($member->email) &&
            $mail->hasFrom('ourHardcoded@address.com', 'OurHardcodedName InWelcomeEmailPhp') &&
            $mail->hasReplyTo('taylor@example.com', 'Árvíztűrő Tükörfúrógép') &&
            $mail->hasSubject('Welcome Email');
        });

        $mailable->assertHasSubject('Welcome Email');
        $mailable->assertSeeInHtml($member->name);
        $mailable->assertSeeInHtml($member->phone_number);
        $mailable->assertSeeInHtml('Successful registration');
        $mailable->assertSeeInHtml('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInHtml('A hardcoded company name');
        $mailable->assertSeeInHtml('book_logo_64x64.png');  //Does it do what it should?
        $mailable->assertSeeInText($member->name);
        $mailable->assertSeeInText($member->phone_number);
        $mailable->assertSeeInText('Successful registration');
        $mailable->assertSeeInText('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInText('A hardcoded company name');
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
