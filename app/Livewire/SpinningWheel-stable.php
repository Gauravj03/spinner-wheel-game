<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Spin;
use App\Models\BalanceLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpinningWheel extends Component
{
    public $result;
    public $balance;
    public $isLoading = false; // To show loading state during spin
    public $spinHistory = []; // New public property to hold spin history

    protected $listeners = ['spinCompleted' => 'storeResult','balanceUpdated' => 'updateBalance'];

    /**
     * Mount is called once when the component is initialized.
     * Use it to set initial properties, like the user's balance.
     */
    public function mount()
    {
        $this->updateBalance();
        $this->loadSpinHistory(); // Load spin history on mount
    }

    /**
     * Updates the local balance property from the authenticated user.
     */
    public function updateBalance()
    {
        $user = Auth::user();
        if ($user) {
            $this->balance = $user->balance;
            // Refresh user model from DB to get latest balance
            $user->refresh();
            $this->balance = $user->balance;
           // $this->result = "Balance topped up by $5!";
        } else {
            $this->balance = 0; // Default to 0 if no user is authenticated
        }
    }

    /**
     * Loads the spin history for the authenticated user.
     */
    protected function loadSpinHistory()
    {
        $user = Auth::user();
        if ($user) {
            // Fetch spins, ordered by latest first
            $this->spinHistory = $user->spins()->latest()->get();
        } else {
            $this->spinHistory = collect(); // Empty collection if no user
        }
    }

    /**
     * Handles the "Top Up" action.
     * Adds a predefined amount to the user's balance.
     */
    public function topUp()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('message', 'Please log in to top up your balance.');
            return;
        }

        $topUpAmount = 5; // Predefined amount to add

        $user->increment('balance', $topUpAmount);
        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => $topUpAmount,
            'type' => 'top_up',
        ]);

        $this->updateBalance();
        $this->result = "Balance topped up by {$topUpAmount}!";
    }

    /**
     * Method called from the 'spin' button click.
     * It initiates the spinning animation on the frontend if balance is sufficient.
     */
    public function startSpin()
    {
        $user = Auth::user();
        $cost = 5; // Predefined cost per spin

        if (!$user) {
            session()->flash('message', 'Please log in to spin the wheel.');
            $this->isLoading = false;
            return;
        }

        if ($user->balance < $cost) {
            $this->result = "Insufficient balance. Top up to play!";
            $this->isLoading = false;
            return;
        }

        $this->isLoading = true;
        $this->result = "Spinning...";

        $this->dispatch('startSpinAnimation', ['cost' => $cost]);
    }

    /**
     * Stores the result of the spin in the database and updates user balance.
     * This method is called via a Livewire event from the JavaScript after the wheel animation completes.
     *
     * @param array|null $data Array containing 'value' (reward amount) and 'label' (result text).
     */
    public function storeResult($data = null)
    {
        $cost = 5; // Predefined cost per spin
        $user = Auth::user();

        if (!$user) {
            Log::error('Attempt to store spin result for unauthenticated user.', ['data' => $data]);
            $this->result = "Error: User not authenticated.";
            $this->isLoading = false;
            return;
        }

        if (!is_array($data) || !isset($data['value']) || !isset($data['label'])) {
            Log::error('SpinCompleted event received with invalid or missing data.', ['received_data' => $data]);
            $this->result = "Error: Spin result data missing or invalid.";
            $this->isLoading = false;
            return;
        }

        // Re-check balance just before deduction (defense in depth)
        if ($user->balance < $cost) {
            $this->result = "Insufficient balance for this spin. Please top up.";
            $this->isLoading = false;
            return;
        }

        // Deduct cost of spin
        $user->decrement('balance', $cost);
        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => -$cost,
            'type' => 'spin_cost',
        ]);

        $rewardAmount = $data['value'];
        $resultLabel = $data['label'];

        // Apply reward/loss amount
        if ($rewardAmount !== 0) { // Only increment/decrement if there's an actual change
            $user->increment('balance', $rewardAmount);
            BalanceLog::create([
                'user_id' => $user->id,
                'amount' => $rewardAmount,
                'type' => ($rewardAmount > 0) ? 'spin_reward' : 'spin_loss', // Differentiate type
            ]);
        }

        // Store spin details
        Spin::create([
            'user_id' => $user->id,
            'cost' => $cost,
            'reward' => $rewardAmount,
            'result_label' => $resultLabel,
        ]);

        // Set the result message based on the outcome
        if ($rewardAmount > 0) {
            $this->result = "Congratulations! You {$resultLabel}!";
        } elseif ($rewardAmount < 0) {
            $this->result = "Too bad! You {$resultLabel}.";
        } else {
            $this->result = "Hard luck! You got '{$resultLabel}'.";
        }

        $this->updateBalance();    // Refresh balance displayed on frontend
        $this->loadSpinHistory();  // Load spin history after a new spin
        $this->dispatch('balanceUpdated'); // 
        $this->isLoading = false;  // End loading state
    }

    public function render()
    {
        return view('livewire.spinning-wheel');
    }
}
