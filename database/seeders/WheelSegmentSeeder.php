<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WheelSegment;

class WheelSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $segments = [
            ['label' => 'Win $10', 'value' => 10],
            ['label' => 'Lose $2', 'value' => -2],
            ['label' => 'Try Again', 'value' => 0],
            ['label' => 'Win $3', 'value' => 3],
            ['label' => 'Lose $10', 'value' => -10],
            ['label' => 'Try Again', 'value' => 0],
            ['label' => 'Win $6', 'value' => 6],
            ['label' => 'Lose $5', 'value' => -5],
        ];

        foreach ($segments as $segment) {
            WheelSegment::create($segment);
        }
    }
}
