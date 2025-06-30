<?php

namespace App\Services;

use App\Models\User;
use App\Models\Spin;
use App\Models\BalanceLog;
use Illuminate\Support\Collection; // To ensure spin history is a Collection
use App\Models\WheelSegment;

class GameService
{
    /**
     * Defines the possible outcomes of the spinning wheel.
     * This is the single source of truth for game logic outcomes.
     *
     * @var array
     */
    // protected array $segments = [
    //     ['label' => "Win $10", 'value' => 10],
    //     ['label' => "Lose $2", 'value' => -2],
    //     ['label' => "Try Again", 'value' => 0],
    //     ['label' => "Win $3", 'value' => 3],
    //     ['label' => "Lose $10", 'value' => -10],
    //     ['label' => "Try Again", 'value' => 0],
    //     ['label' => "Win $6", 'value' => 6],
    //     ['label' => "Lose $5", 'value' => -5]
    // ];

    /**
     * The cost of a single spin.
     * @var int
     */
    protected int $spinCost = 5;

    /**
     * The amount added when a user tops up.
     * @var int
     */
    protected int $topUpAmount = 5;

    /**
     * Get the predefined segments/outcomes for the wheel.
     *
     * @return array
     */
    public function getSegments(): array
    {
       // return $this->segments;
        return WheelSegment::orderBy('id')->get()->toArray();
    }

    /**
     * Get the cost of a single spin.
     *
     * @return int
     */
    public function getSpinCost(): int
    {
        return $this->spinCost;
    }

    /**
     * Get the top-up amount.
     *
     * @return int
     */
    public function getTopUpAmount(): int
    {
        return $this->topUpAmount;
    }

    /**
     * Handles topping up the user's balance.
     *
     * @param User $user The user topping up.
     * @return array An array indicating success, message, and new balance.
     */
    public function topUp(User $user): array
    {
        $user->increment('balance', $this->topUpAmount);
        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => $this->topUpAmount,
            'type' => 'top_up',
        ]);

        $user->refresh(); // Refresh user model to get latest balance
        return [
            'status' => 'success',
            'message' => "Balance topped up by ${$this->topUpAmount}!",
            'new_balance' => $user->balance
        ];
    }

    /**
     * Processes a single spin of the wheel.
     * Deducts cost, determines random outcome, applies reward/loss, and logs.
     *
     * @param User $user The user performing the spin.
     * @return array An array containing the spin result, message, and new balance.
     * @throws \Exception If user has insufficient balance.
     */
    public function processSpin(User $user, ?int $frontendIndex = null): array
    {
        if ($user->balance < $this->spinCost) {
            throw new \Exception('Insufficient balance to spin the wheel.');
        }

        // Deduct cost of spin
        $user->decrement('balance', $this->spinCost);
        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => -$this->spinCost,
            'type' => 'spin_cost',
        ]);

        $segments = $this->getSegments();

        // Defensive: make sure segments exist
        if (empty($segments)) {
            throw new \Exception('No wheel segments configured.');
        }

        // Pick segment
        if (!is_null($frontendIndex) && isset($segments[$frontendIndex])) {
            $prize = $segments[$frontendIndex];
        } else {
            $prize = $segments[array_rand($segments)];
        }

        // Randomly select a prize from the defined segments
       // $prize = $this->segments[array_rand($this->segments)];
        // Determine prize from frontend (if sent), else random
    //    if (!is_null($frontendIndex) && isset($this->segments[$frontendIndex])) {
    //         $prize = $this->segments[$frontendIndex];
    //     } else {
    //         $prize = $this->segments[array_rand($this->segments)];
    //     }


        $rewardAmount = $prize['value'];
        $resultLabel = $prize['label'];

        // Apply reward/loss amount
        if ($rewardAmount !== 0) {
            $user->increment('balance', $rewardAmount);
            BalanceLog::create([
                'user_id' => $user->id,
                'amount' => $rewardAmount,
                'type' => ($rewardAmount > 0) ? 'spin_reward' : 'spin_loss',
            ]);
        }

        // Store spin details
        Spin::create([
            'user_id' => $user->id,
            'cost' => $this->spinCost,
            'reward' => $rewardAmount,
            'result_label' => $resultLabel,
        ]);

        // Refresh user balance to return the latest
        $user->refresh();

        // Determine result message
        $message = '';
        if ($rewardAmount > 0) {
            $message = "Congratulations! You {$resultLabel}!";
        } elseif ($rewardAmount < 0) {
            $message = "Too bad! You {$resultLabel}.";
        } else {
            $message = "Hard luck! You got '{$resultLabel}'.";
        }

        return [
            'status' => 'success',
            'message' => $message,
            'result_label' => $resultLabel,
            'reward_amount' => $rewardAmount,
            'new_balance' => $user->balance
        ];
    }

    /**
     * Get the authenticated user's current balance.
     *
     * @param User $user The user whose balance is to be retrieved.
     * @return int The user's current balance.
     */
    public function getUserBalance(User $user): int
    {
        $user->refresh(); // Ensure we get the latest balance from DB
        return $user->balance;
    }

    /**
     * Get the authenticated user's spin history.
     *
     * @param User $user The user whose spin history is to be retrieved.
     * @return Collection A collection of Spin models.
     */
    public function getUserSpinHistory(User $user): Collection
    {
        return $user->spins()->latest()->get();
    }
}
