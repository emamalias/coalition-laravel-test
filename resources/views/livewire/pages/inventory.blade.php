<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;

new class extends Component {


    #[Validate('required', message: 'Required')]
    #[Validate('min:3', message: 'Atleast 3 characters')]
    public string $productName;

    #[Validate('required', message: 'Required')]
    #[Validate('numeric', message: 'Must be a number')]
    #[Validate('min:1', message: 'Atleast 1')]
    public int $quantity;

    #[Validate('required', message: 'Required')]
    #[Validate('numeric', message: 'Must be a number')]
    #[Validate('min:0.01', message: 'Atleast 0.01')]
    public float $price;

    public string $dateSubmitted;

    public int | null $updateProductIndex = null;

    public Collection $products;

    public float $totalValue;

    protected string $jsonPath = 'app/public/inventory.json';

    public function mount()
    {
        $this->productName = '';
        $this->quantity = 0;
        $this->price = 0.0;

        if (file_exists(storage_path($this->jsonPath))) {
            $jsonProducts = file_get_contents(storage_path($this->jsonPath));
            $productsArray = json_decode($jsonProducts, true);
            $this->products = collect($productsArray);
        } else {
            $this->products = collect([]);
        }

        $this->totalValue = 0;
    }

    protected function resetForm()
    {
        $this->productName = '';
        $this->quantity = 0;
        $this->price = 0.0;
        $this->updateProductIndex = null;
    }

    public function saveProduct(): void
    {
        $this->validate();

        if ($this->updateProductIndex !== null) {
            $this->updateProduct($this->updateProductIndex);
        } else {
            $this->createProduct();
        }

        $this->resetForm();

        $this->calculateTotalValue();
    }

    public function createProduct()
    {
        $this->dateSubmitted = now()->toDateTimeString();

        $newProduct = [
            'productName' => $this->productName,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'dateSubmitted' => $this->dateSubmitted,
        ];

        $this->products->push($newProduct);

        $this->syncProductsToJsonFile();
    }

    public function deleteProduct($index)
    {
        $this->products->forget($index);

        $this->calculateTotalValue();

        $this->syncProductsToJsonFile();
    }

    public function updateProduct($index)
    {
        $this->products[$index] = [
            'productName' => $this->productName,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'dateSubmitted' => now()->toDateTimeString(),
        ];

        $this->syncProductsToJsonFile();
    }

    public function requestUpdate(int $index): void
    {
        // find product by index
        $product = $this->products->get($index);

        if ($product === null) {
            return;
        }

        $this->updateProductIndex = $index;
        $this->productName = $product['productName'];
        $this->quantity = $product['quantity'];
        $this->price = $product['price'];
    }

    public function calculateTotalValue(): void
    {
        $this->totalValue = $this->products->sum(function ($product) {
            return $product['quantity'] * $product['price'];
        });
    }


    public function syncProductsToJsonFile()
    {
        $productsArray = $this->products->toArray();
        $jsonProducts = json_encode($productsArray, JSON_PRETTY_PRINT);
        file_put_contents(storage_path($this->jsonPath), $jsonProducts);
    }

}; ?>

<div wire:init='calculateTotalValue'>
    <h1 class="text-4xl font-bold">Inventory Management</h1>

    <form
        wire:submit.prevent="saveProduct"
        class="flex w-full gap-5 mt-5 @if ($errors->any()) items-center @else items-end @endif"
    >
        <div class="flex-1 flex flex-col gap-2">
            <label for="product-name">Product Name</label>
            <input type="text" id="product-name" wire:model="productName" class="p-2 border border-gray-300 rounded-md">
            @error('productName') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" wire:model="quantity" class="p-2 border border-gray-300 rounded-md">
            @error('quantity') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="price">Price</label>
            <input type="text" id="price" wire:model="price" class="p-2 border border-gray-300 rounded-md">
            @error('price') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col gap-2">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded-md px-5 font-bold">
                @if ($updateProductIndex !== null)
                    <span>Update Product</span>
                @else
                    <span>Add Product</span>
                @endif
            </button>
        </div>
    </form>

    <table class="w-full my-10 rounded-xl border border-gray-300">
        <thead>
            <tr>
                <th class="p-2 border border-gray-300 text-left">Product Name</th>
                <th class="p-2 border border-gray-300 text-right">Quantity in stock</th>
                <th class="p-2 border border-gray-300 text-right">Price per item</th>
                <th class="p-2 border border-gray-300 text-left">Datetime submitted</th>
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
                @foreach ($products as $key => $product)
                    <tr>
                        <td class="p-2 border border-gray-300 text-left">{{ $product['productName'] }}</td>
                        <td class="p-2 border border-gray-300 text-right">${{ $product['quantity'] }}</td>
                        <td class="p-2 border border-gray-300 text-right">${{ $product['price'] }}</td>
                        <td class="p-2 border border-gray-300 text-left">{{ $product['dateSubmitted'] }}</td>
                        <td class="p-2 border border-gray-300 text-right">${{ number_format($product['quantity'] * $product['price'], 2) }}</td>
                        <td class="p-2 border border-gray-300 text-left">
                            <button class="text-blue-500 underline mx-1" wire:click='requestUpdate(@js($key))'>U</button>
                            <button class="text-red-500 underline mx-1" wire:click='deleteProduct(@js($key))'>D</button>
                        </td>
                    </tr>
                @endforeach
            @endif

        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="p-2 border border-gray-300 text-right font-bold">Total:</td>
                <td class="p-2 border border-gray-300 text-right">${{ number_format($totalValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
