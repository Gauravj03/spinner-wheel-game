<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\BalanceLog;

class TopUpButton extends Component
{
    public $balance;

    protected $listeners = ['refreshBalance' => 'getBalance'];

    public function mount()
    {
        $this->getBalance();
    }

    public function getBalance()
    {
        $user = Auth::user();
        $this->balance = $user ? $user->balance : 0;
    }

    public function topUp()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('message', 'Please log in to top up your balance.');
            return;
        }

        $amount = 5;
        $user->increment('balance', $amount);

        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'top_up',
        ]);

        $this->getBalance();

        // Notify parent or other components if needed
        $this->dispatch('balanceUpdated');
        session()->flash('message', 'Balance topped up by $5!');
    }

    public function render()
    {
        return view('livewire.top-up-button');
    }
}
