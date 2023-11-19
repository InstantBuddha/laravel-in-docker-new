<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\MemberRegistered;
use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        $toEmail = $member->email;
        $name = $member->name;
        $phone_number = $member->phone_number;

        Mail::to($toEmail)->send(new WelcomeEmail($name, $phone_number));
    }
}
