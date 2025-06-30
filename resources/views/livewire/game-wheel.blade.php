<div x-data>
    <div class="p-4 border rounded">
        <h2 class="text-xl">Balance: <span class="font-bold">{{ $balance }}</span></h2>
        <button wire:click="topUp" class="bg-green-500 text-white px-4 py-2 mt-2 rounded">Top Up +1</button>
        <button wire:click="spin" class="bg-blue-500 text-white px-4 py-2 mt-2 ml-2 rounded">Spin</button>
    </div>

    <div class="mt-4">
        <template x-if="$wire.lastResult">
            <p class="text-lg">Result: <strong>{{ $lastResult }}</strong></p>
        </template>
    </div>
</div>

<!-- <div x-data="{ open: false }" class="p-4">
    <button @click="open = !open" class="bg-blue-500 text-white px-4 py-2 rounded">
        Toggle Message
    </button>
    <p x-show="open" class="mt-4 text-green-600">Alpine is working!</p>
</div> -->
