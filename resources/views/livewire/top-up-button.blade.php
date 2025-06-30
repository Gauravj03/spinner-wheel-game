<div class="">
    <!-- <h2 class="text-2xl font-bold text-gray-800">Your Balance: 
        <span class="text-green-600">${{ $balance }}</span>
    </h2> -->
    <button wire:click="topUp"
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-2 rounded-full shadow-md shadow-blue-200 transition duration-300 ease-in-out transform hover:scale-105">
        Top Up (+$5)
    </button>
</div>

@if (session()->has('message'))
    <div class="mt-2 text-sm text-green-600">
        {{ session('message') }}
    </div>
@endif
