<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex gap-5 min-h-[400px] flex-col items-center justify-center my-auto">
    <h1 class="text-4xl font-bold">Coalition Laravel Follow-up Test</h1>

    <a href="{{ route('inventory') }}" class="text-2xl text-blue-600 underline" wire:navigate>Go to inventory management</a>
</div>
