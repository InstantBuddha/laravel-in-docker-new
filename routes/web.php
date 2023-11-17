<?php

use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Route;

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
    return 'Hello World';
});

Route::get('/greeting', function () {
    return 'Hello World';
});

Route::get('/testemail', function () {
    $name = 'John Doe';
    $phone_number= '0036701234567';
    Mail::to('testreceiver@example.com')->send(new WelcomeEmail($name, $phone_number));
});