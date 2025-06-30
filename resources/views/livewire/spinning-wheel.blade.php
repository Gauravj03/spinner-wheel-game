<div class="flex flex-col lg:flex-row items-center lg:items-start justify-center py-10 min-h-screen bg-gray-100 font-inter lg:gap-8">
    <!-- Left Column: Wheel, Balance, Top Up, Result -->
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md lg:max-w-lg">
        <div class="h-[430px]">
            <!-- Balance and Top Up -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Your Balance: <span class="text-green-600">${{ $balance }}</span></h2>
                <!-- <button
                    wire:click="topUp"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    Top Up (+5$)
                </button> -->
                @livewire('top-up-button')
            </div>

            <!-- Canvas and Spin Button -->
            <div class="relative mx-auto py-14" wire:ignore>
                    <canvas id="wheelCanvas" class="w-full h-auto block rounded-full"></canvas>

                    <!-- Spin Button Centered -->
                    <button
                        wire:click="startSpin"
                        wire:loading.attr="disabled"
                        wire:target="startSpin"
                        class="absolute inset-0 flex justify-center items-center z-10">
                        <div class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2 rounded-full shadow-md transition-all duration-300 ease-in-out">
                            <span wire:loading.remove wire:target="startSpin">SPIN</span>
                            <span wire:loading wire:target="startSpin">SPINNING...</span>
                        </div>
                    </button>

                    <!-- Pointer aligned on right -->
                    <div class="absolute top-1/2 right-[5.5rem] transform -translate-y-1/2 pointer-events-none">
                        <svg class="w-12 h-12 text-blue-900" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 18L8 12L14 6L14 18Z"/>
                        </svg>
                    </div>
            </div>


            <!-- Result Display -->
            @if ($result)
                <div class="text-2xl font-bold text-center {{ str_contains($result, 'Insufficient') || str_contains($result, 'Error') || str_contains($result, 'Lose') || str_contains($result, 'Hard luck') ? 'text-red-600' : 'text-green-700' }}">
                    {{ $result }}
                </div>
            @endif

            <!-- Session Flash Message for Top Up/Login -->
            @if (session()->has('message'))
                <div class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-md text-center">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Spin History -->
<div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md lg:max-w-lg">
    <h3 class="text-xl font-bold text-blue-900 mb-4 text-center">Spin History</h3>

    @if ($spinHistory->isEmpty())
        <p class="text-blue-700 text-center text-sm">No spins recorded yet.</p>
    @else
        <div class="overflow-x-auto h-96 overflow-y-auto rounded-lg">
            <table class="min-w-full text-sm rounded-lg shadow-sm shadow-blue-100">
                <thead class="bg-blue-100 sticky top-0 z-10 text-blue-900">
                    <tr>
                        <th class="py-2 px-4 text-left font-semibold">Time</th>
                        <th class="py-2 px-4 text-left font-semibold">Result</th>
                        <th class="py-2 px-4 text-left font-semibold">Cost</th>
                        <th class="py-2 px-4 text-left font-semibold rounded-tr-lg">Reward</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spinHistory as $index => $spin)
                        <tr class="{{ $index % 2 === 0 ? 'bg-blue-50' : 'bg-blue-100' }} border-b border-blue-200">
                            <td class="py-2 px-4 whitespace-nowrap text-blue-800">
                                {{ $spin->created_at->format('M d, H:i') }}
                            </td>
                            <td class="py-2 px-4 {{ $spin->reward > 0 ? 'text-green-600' : ($spin->reward < 0 ? 'text-red-600' : 'text-blue-700') }}">
                                {{ $spin->result_label }}
                            </td>
                            <td class="py-2 px-4 text-blue-800">
                                -${{ $spin->cost }}
                            </td>
                            <td class="py-2 px-4 whitespace-nowrap font-medium {{ $spin->reward > 0 ? 'text-green-600' : ($spin->reward < 0 ? 'text-red-600' : 'text-blue-700') }}">
                                @if ($spin->reward > 0)
                                    +${{ $spin->reward }}
                                @elseif ($spin->reward < 0)
                                    -${{ abs($spin->reward) }}
                                @else
                                    $0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const wheelCanvas = document.getElementById("wheelCanvas");
        const ctx = wheelCanvas.getContext("2d");

        const segments = [
            { label: "Win $10", value: 10 },
            { label: "Lose $2", value: -2 },
            { label: "Try Again", value: 0 },
            { label: "Win $3", value: 3 },
            { label: "Lose $10", value: -10 },
            { label: "Try Again", value: 0 },
            { label: "Win $6", value: 6 },
            { label: "Lose $5", value: -5 }
        ];

        // New color palette with less contrast
        const colors = [
            "#81C784", // Light Green (Win)
            "#EF9A9A", // Light Red (Lose)
            "#FFEB3B", // Light Amber (Try Again)
            "#A5D6A7", // Muted Green (Win)
            "#FFCDD2", // Muted Red (Lose)
            "#FFEE58", // Muted Amber (Try Again)
            "#66BB6A", // Medium Green (Win)
            "#EF5350"  // Medium Red (Lose)
        ];
        let angle = 0;
        let spinning = false;

        function resizeCanvas() {
                const rect = wheelCanvas.getBoundingClientRect();
                wheelCanvas.width = rect.width;
                wheelCanvas.height = rect.height;
                drawWheel();
            }



        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function drawWheel() {
            const numSegments = segments.length;
            const arcSize = 2 * Math.PI / numSegments;
            const centerX = wheelCanvas.width / 2;
            const centerY = wheelCanvas.height / 2;
            const radius = Math.min(centerX, centerY);

            ctx.clearRect(0, 0, wheelCanvas.width, wheelCanvas.height);
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate((angle * Math.PI) / 180);
            ctx.translate(-centerX, -centerY);

            for (let i = 0; i < numSegments; i++) {
                const startAngle = i * arcSize;
                const endAngle = startAngle + arcSize;

                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fillStyle = colors[i % colors.length];
                ctx.fill();
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 1; // Slightly thinner borders for a softer look
                ctx.stroke();

                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + arcSize / 2);
                ctx.textAlign = "right";
                ctx.fillStyle = "#333"; // Darker text for better contrast on lighter backgrounds
                ctx.font = "bold " + (radius * 0.09) + "px Inter, sans-serif";
                ctx.fillText(segments[i].label, radius * 0.85, 0);
                ctx.restore();
            }
            ctx.restore();
        }

        Livewire.on('startSpinAnimation', () => {
            if (spinning) return;
            spinning = true;

            const spins = Math.floor(Math.random() * 5) + 5;
            const angleOffset = Math.random() * 360;
            const totalRotation = spins * 360 + angleOffset;

            const duration = 3000;
            const start = performance.now();

            function animate(currentTime) {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = 1 - Math.pow(1 - progress, 3);

                angle = (totalRotation * easedProgress) % 360;
                drawWheel();

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    const segmentAngle = 360 / segments.length;
                    // Pointer is on the right side (0 degrees in canvas coordinates)
                    const pointerPosition = 0; // Winning segment aligns with 0 degrees (right)

                    const finalAngleUnderPointer = (pointerPosition - angle % 360 + 360) % 360; // Normalize angle
                    const calculatedSegmentIndex = Math.floor(finalAngleUnderPointer / segmentAngle);

                    const prize = segments[Math.min(calculatedSegmentIndex, segments.length - 1)];

                    Livewire.dispatch('spinCompleted', { data: { value: prize.value, label: prize.label } });
                    spinning = false;
                }
            }
            requestAnimationFrame(animate);
        });

        drawWheel();

        // setTimeout(() => {
        //     const canvas = document.getElementById('wheelCanvas');
        //     if (canvas) {
        //         canvas.style.width = canvas.offsetHeight + 'px';
        //     }
        // }, 50);
    });

     document.addEventListener('livewire:update', () => {
            if (typeof resizeCanvas === 'function') {
                resizeCanvas();
                conole.log('testt');
            }
        });

</script>
@endpush
