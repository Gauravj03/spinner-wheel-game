<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Spin;
use App\Models\BalanceLog;
use App\Services\GameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpinningWheel extends Component
{
    public $result;
    public $balance;
    public $isLoading = false; // To show loading state during spin
    public $spinHistory = []; // New public property to hold spin history
    protected GameService $gameService;

    protected $listeners = ['spinCompleted' => 'storeResult','balanceUpdated' => 'updateBalance'];

    public function boot(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Mount is called once when the component is initialized.
     * Use it to set initial properties, like the user's balance.
     */
    public function mount()
    {
       // $this->gameService = $gameService;

        $user = Auth::user();

        if ($user) {
            $this->balance = $this->gameService->getUserBalance($user);
            $this->spinHistory = $this->gameService->getUserSpinHistory($user);
        }
    }

    /**
     * Updates the local balance property from the authenticated user.
     */
    // public function updateBalance()
    // {
    //     $user = Auth::user();
    //     if ($user) {
    //         $this->balance = $user->balance;
    //         // Refresh user model from DB to get latest balance
    //         $user->refresh();
    //         $this->balance = $user->balance;
    //        // $this->result = "Balance topped up by $5!";
    //     } else {
    //         $this->balance = 0; // Default to 0 if no user is authenticated
    //     }
    // }

    public function updateBalance()
    {
        $user = Auth::user();

        if ($user) {
            $this->balance = $this->gameService->getUserBalance($user);
        }
    }

    /**
     * Loads the spin history for the authenticated user.
     */
    // protected function loadSpinHistory()
    // {
    //     $user = Auth::user();
    //     if ($user) {
    //         // Fetch spins, ordered by latest first
    //         $this->spinHistory = $user->spins()->latest()->get();
    //     } else {
    //         $this->spinHistory = collect(); // Empty collection if no user
    //     }
    // }

    protected function loadSpinHistory()
    {
        $user = Auth::user();
        $this->spinHistory = $user ? $this->gameService->getUserSpinHistory($user) : collect();
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

        $result = $this->gameService->topUp($user);
        $this->balance = $result['new_balance'];
        $this->result = $result['message'];
    }

    /**
     * Method called from the 'spin' button click.
     * It initiates the spinning animation on the frontend if balance is sufficient.
     */
    public function startSpin()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('message', 'Please log in to spin the wheel.');
            $this->isLoading = false;
            return;
        }

        if ($this->balance < $this->gameService->getSpinCost()) {
            $this->result = "Insufficient balance. Top up to play!";
            $this->isLoading = false;
            return;
        }

        $this->isLoading = true;
        $this->result = "Spinning...";

        $this->dispatch('startSpinAnimation', ['cost' => $this->gameService->getSpinCost()]);
    }

    /**
     * Stores the result of the spin in the database and updates user balance.
     * This method is called via a Livewire event from the JavaScript after the wheel animation completes.
     *
     * @param array|null $data Array containing 'value' (reward amount) and 'label' (result text).
     */
        public function storeResult($data = null)
        {
                    $user = Auth::user();

        if (!$user || !is_array($data) || !isset($data['label']) || !isset($data['value'])) {
            $this->result = "Invalid or missing spin result.";
            $this->isLoading = false;
            return;
        }

        // Find the index of the segment by label (safer than trusting frontend index blindly)
        $segments = $this->gameService->getSegments();
        $index = collect($segments)->search(fn($segment) => $segment['label'] === $data['label']);

        $result = $this->gameService->processSpin($user, is_int($index) ? $index : null);

        $this->balance = $result['new_balance'];
        $this->result = $result['message'];
        $this->spinHistory = $this->gameService->getUserSpinHistory($user);
        $this->isLoading = false;

        $this->dispatch('balanceUpdated');
        }


    protected function findSegmentIndex(string $label): ?int
    {
        foreach ($this->gameService->getSegments() as $index => $segment) {
            if ($segment['label'] === $label) {
                return $index;
            }
        }
        return null;
    }


    public function render()
    {
        return view('livewire.spinning-wheel');
    }
}
