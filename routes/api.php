<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController; // Import your new controller

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

// --- Authentication Endpoint (for issuing tokens) ---
// This is typically a route where a user logs in with email/password
// and receives a token back. For simplicity, we'll use a basic example.
// In a real app, you'd likely have a dedicated AuthController.
Route::post('/tokens/create', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required', // E.g., 'iPhone', 'Web Browser'
    ]);

    // http://localhost:8000/api/tokens/create

    $user = App\Models\User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Delete existing tokens if desired (for single active session)
    // $user->tokens()->delete();

    // Create a new token
    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json(['token' => $token]);
});


// --- Protected API Endpoints for the Game ---
Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user's balance
    Route::get('/game/balance', [GameController::class, 'getBalance']); // http://localhost:8000/api/game/balance

    // Perform a spin
    Route::post('/game/spin', [GameController::class, 'spin']);

    // Get authenticated user's spin history
    Route::get('/game/history', [GameController::class, 'getSpinHistory']);

    // Get authenticated user details (example)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
