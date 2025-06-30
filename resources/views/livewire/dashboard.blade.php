<div class="p-8 bg-gray-100 min-h-screen font-inter">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">

        <!-- Greeting -->
        <h1 class="text-3xl font-bold text-blue-900">Welcome, {{ $username }}!</h1>

        <!-- Balance & Play Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-lg text-blue-800">
                Available Balance:
                <span class="font-semibold text-green-600">${{ $balance }}</span>
            </p>
            <a href="{{ route('spin-wheel') }}"
               class="text-sm bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-sm transition">
                Play The Game
            </a>
        </div>

        <!-- Recent Spins -->
        <div class="bg-white shadow-md shadow-blue-100 rounded-xl p-4">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">Recent Spins</h2>

            @if($recentSpins->isEmpty())
                <p class="text-blue-700 text-sm">You haven't spun the wheel yet.</p>
            @else
                <ul class="rounded-md overflow-hidden divide-y divide-blue-100">
                    @foreach($recentSpins as $index => $spin)
                        <li class="flex justify-between px-4 py-3 text-sm {{ $index % 2 === 0 ? 'bg-blue-50' : 'bg-blue-100' }}">
                            <span class="text-blue-700">{{ $spin->created_at->format('M d, H:i') }}</span>
                            <span class="font-medium text-blue-900">{{ $spin->result_label }}</span>
                            <span class="{{ $spin->reward > 0 ? 'text-green-600' : ($spin->reward < 0 ? 'text-red-600' : 'text-blue-700') }}">
                                {{ $spin->reward >= 0 ? '+' : '' }}${{ $spin->reward }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
