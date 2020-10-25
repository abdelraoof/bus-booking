<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| Issuing API Tokens
|--------------------------------------------------------------------------
|
| API tokens that may be used to authenticate API requests.
| When making requests using API tokens,
| pass the token in the Authorization header as a Bearer token.
|
*/

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
        'device_name' => 'required|string|max:255',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ensure that incoming requests contain a valid API token header.
| With sanctum authentication guard.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
