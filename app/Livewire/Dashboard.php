<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Spin;

class Dashboard extends Component
{
    public $username;
    public $balance;
    public $recentSpins = [];

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $this->username = $user->name;
            $this->balance = $user->balance;
            $this->recentSpins = $user->spins()->latest()->take(5)->get();
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

