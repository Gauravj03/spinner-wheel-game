<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GameService; // Import the GameService
use Illuminate\Support\Facades\Log; // Import Log facade

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
            'user_id' => $user->id,
            'balance' => $balance,
        ]);
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
            'message' => $result['message'],
            'result_label' => $result['result_label'],
            'reward_amount' => $result['reward_amount'],
            'new_balance' => $result['new_balance'],
        ], 200);

    } catch (\Exception $e) {
        if ($e->getMessage() === 'Insufficient balance to spin the wheel.') {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        Log::error('API Spin failed: ' . $e->getMessage(), ['user_id' => $user->id]);
        return response()->json(['message' => 'An unexpected error occurred.'], 500);
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
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $spins = $this->gameService->getUserSpinHistory($user);

        return response()->json([
            'user_id' => $user->id,
            'spin_history' => $spins,
        ]);
    }
}
