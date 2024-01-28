<?php

declare(strict_types=1);

namespace App\Models;

use App\Mail\WelcomeEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Member extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'zipcode',
        'city',
        'address',
        'comment',
        'mailing_list',
    ];

    protected static function booted(): void {
        static::created(function (Member $member) {
            Mail::send(new WelcomeEmail($member));
        });
    }
}
