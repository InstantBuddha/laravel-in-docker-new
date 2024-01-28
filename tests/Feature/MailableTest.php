<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\WelcomeEmail;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailableTest extends TestCase
{
    use RefreshDatabase;
    public function test_mailable_content(): void
    {
        Mail::fake();

        $member = Member::factory()->make();

        $mailable = new WelcomeEmail($member);

        Mail::send($mailable);

        Mail::assertSent(WelcomeEmail::class, function (WelcomeEmail $mail) use ($member) {
            $mail->hasTo($member->email);
            $mail->hasFrom('set_in_env@example.hu', 'OurName SetInDotEnv');
            $mail->hasReplyTo('replyToAddress@inmailphp.com', 'OurReplyToAddress InMailPhp');
            $mail->hasSubject('Welcome Email');
            $mail->hasBcc(['bccAddress1@setin.env', 'bccAddress2@setin.env']);
            return true;
        });

        $mailable->assertSeeInHtml($member->name);
        $mailable->assertSeeInHtml($member->phone_number);
        $mailable->assertSeeInHtml('Successful registration');
        $mailable->assertSeeInHtml('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInHtml('A hardcoded company name');
        if ($member->address) {
            $mailable->assertSeeInHtml($member->address);
        }
        if ($member->comment) {
            $mailable->assertSeeInHtml($member->comment);
        }
        if ($member->mailing_list) {
            $mailable->assertSeeInHtml('You have chosen to receive our newsletter.');
        }
        $mailable->assertSeeInText($member->name);
        $mailable->assertSeeInText($member->phone_number);
        $mailable->assertSeeInText('Successful registration');
        $mailable->assertSeeInText('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInText('A hardcoded company name');
        if ($member->address) {
            $mailable->assertSeeInText($member->address);
        }
        if ($member->comment) {
            $mailable->assertSeeInText($member->comment);
        }
        if ($member->mailing_list) {
            $mailable->assertSeeInText('You have chosen to receive our newsletter.');
        }
    }
}
