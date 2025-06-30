<?php

namespace App\Livewire;
// namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Spin;
use App\Models\BalanceLog;
use Illuminate\Support\Facades\Auth;

class GameWheel extends Component
{
    public $balance;
    public $lastResult;

    public function mount()
    {
        $this->balance = auth()->user()->balance;
    }

    public function topUp()
    {
        auth()->user()->increment('balance', 1);
        BalanceLog::create([
            'user_id' => auth()->id(),
            'amount' => 1,
            'type' => 'top-up'
        ]);
        $this->balance++;
    }

    public function spin()
    {
        $cost = 5;
        if ($this->balance < $cost) {
            $this->lastResult = "Insufficient Balance";
            return;
        }

        auth()->user()->decrement('balance', $cost);
        BalanceLog::create([
            'user_id' => auth()->id(),
            'amount' => -$cost,
            'type' => 'spin'
        ]);

        $outcomes = [
            ['label' => '+10 Credits', 'reward' => 10],
            ['label' => '-5 Credits', 'reward' => -5],
            ['label' => 'Try Again', 'reward' => 0],
        ];
        $result = $outcomes[array_rand($outcomes)];

        auth()->user()->increment('balance', $result['reward']);
        BalanceLog::create([
            'user_id' => auth()->id(),
            'amount' => $result['reward'],
            'type' => 'reward'
        ]);

        Spin::create([
            'user_id' => auth()->id(),
            'cost' => $cost,
            'reward' => $result['reward'],
            'result_label' => $result['label']
        ]);

        $this->balance += $result['reward'];
        $this->lastResult = $result['label'];
    }

    public function render()
    {
        return view('livewire.game-wheel');
    }
}

