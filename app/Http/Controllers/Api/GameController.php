<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GameService; // Import the GameService
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Http\Requests\CreateTokenRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class GameController extends Controller
{
    protected GameService $gameService;

    /**
     * Constructor to inject the GameService.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

        public function create(CreateTokenRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
                'data' => null
            ], 401);
        }

        // Create token
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Token created successfully.',
            'data' => [
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Get the authenticated user's current balance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $balance = $this->gameService->getUserBalance($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User balance fetched successfully.',
            'data' => [
                'user_id' => $user->id,
                'balance' => $balance,
            ]
        ], 200);
    }

    /**
     * Simulate a spin of the wheel via API.
     * This method will deduct the cost, calculate a random outcome,
     * apply reward/loss, and store the spin history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function spin(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try {
        $frontendIndex = $request->input('segment_index'); // Optional
        $result = $this->gameService->processSpin($user, $frontendIndex);

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'result_label' => $result['result_label'],
                'reward_amount' => $result['reward_amount'],
                'new_balance' => $result['new_balance'],
            ]
        ], 200);

    } catch (\Exception $e) {
        if ($e->getMessage() === 'Insufficient balance to spin the wheel.') {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        Log::error('API Spin failed: ' . $e->getMessage(), ['user_id' => $user->id]);
        
        return response()->json([
            'status' => 'failed',
            'message' => 'An unexpected error occurred.',
            'data' => null
        ], 500);
    }
}


    /**
     * Get the authenticated user's spin history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpinHistory(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
            'status' => 'failed',
            'message' => 'Unauthorized',
            'data' => null
        ], 401);
        }

        $spins = $this->gameService->getUserSpinHistory($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User spin history list fetched successfully',
            'data' => [
                'user_id' => $user->id,
                'spin_history' => $spins,
            ]
        ], 200);
    }
}
