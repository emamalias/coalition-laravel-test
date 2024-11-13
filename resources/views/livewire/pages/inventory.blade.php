<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {


    public string $productName;
    public int $quantity;
    public float $price;

    public Collection $products;

    public function mount()
    {
        $this->productName = '';
        $this->quantity = 0;
        $this->price = 0.0;

        $this->products = collect([]);
    }

}; ?>

<div>
    <h1 class="text-4xl font-bold">Inventory Management</h1>

    <form wire:submit.prevent="createProduct" class="flex w-full gap-5 mt-5 items-end">
        <div class="flex-1 flex flex-col gap-2">
            <label for="product-name">Product Name</label>
            <input type="text" id="product-name" wire:model="productName" class="p-2 border border-gray-300 rounded-md">
        </div>

        <div class="flex flex-col gap-2">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" wire:model="quantity" class="p-2 border border-gray-300 rounded-md">
        </div>

        <div class="flex flex-col gap-2">
            <label for="price">Price</label>
            <input type="number" id="price" wire:model="price" class="p-2 border
            border-gray-300 rounded-md">
        </div>
        <div class="flex flex-col gap-2">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded-md px-5 font-bold">Add Product</button>
        </div>
    </form>

    <table class="w-full my-10 rounded-xl border border-gray-300">
        <thead>
            <tr>
                <th class="p-2 border border-gray-300 text-left">Product Name</th>
                <th class="p-2 border border-gray-300 text-left">Quantity in stock</th>
                <th class="p-2 border border-gray-300 text-right">Price per item</th>
                <th class="p-2 border border-gray-300 text-right">Datetime submitted</th>
                <th class="p-2 border border-gray-300 text-right">Total value</th>
                <th class="p-2 border border-gray-300 text-right"></th>
            </tr>
        </thead>
        <tbody>
            @if (count($products) === 0)
                <tr>
                    <td class="py-10 p-2 border border-gray-300 text-center text-gray-500" colspan="6">No products found</td>
                </tr>
            @else
                @foreach ($products as $product)
                    <tr>
                        <td class="p-2 border border-gray-300">{{ $product['productName'] }}</td>
                        <td class="p-2 border border-gray-300">{{ $product['quantity'] }}</td>
                        <td class="p-2 border border-gray-300">{{ $product['price'] }}</td>
                        <td class="p-2 border border-gray-300">{{ $product['dateSubmitted'] }}</td>
                        <td class="p-2 border border-gray-300">{{ $product['quantity'] * $product['price'] }}</td>
                        <td class="p-2 border border-gray-300">edit</td>
                    </tr>
                @endforeach
            @endif

        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" class="p-2 border border-gray-300 text-right font-bold">Total:</td>
                <td class="p-2 borderborder-gray-300 text-right">0</td>
            </tr>
        </tfoot>
    </table>
</div>
