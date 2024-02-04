<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login']]);
    }

    public function login(Request $request): Response
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            return response()->json(['token' => $user->createToken('ApiToken')->plainTextToken]);
        }

        return response(status: Response::HTTP_UNAUTHORIZED);
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out',]);
    }

    public function authTest(Request $request)
    {
        return response()->json();
    }
}
