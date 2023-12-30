<?php

use App\Mail\WelcomeEmail;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    return 'Hello World HOME';
});

Route::get('/greeting', function () {
    return 'Hello World';
});

Route::get('/testemail', function () {
    $exampleMember = Member::factory()->make();

    Mail::send(new WelcomeEmail($exampleMember));
    return 'Test email sent';
});
