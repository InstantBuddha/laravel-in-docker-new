<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->get('/auth/something', [AuthController::class, 'something']);

Route::middleware(['auth:sanctum'])->post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->apiResource('members', MemberController::class)->except(['store']);

Route::post('/register-new-member', [MemberController::class, 'store']);

Route::post('/corstest', fn() => response('success yeah', 200));

Route::get('/ping', static fn() => 'pong');