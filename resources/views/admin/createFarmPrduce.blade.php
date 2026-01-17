{{-- resources/views/manager/farm-produce/create.blade.php --}}

<x-layouts.mapp title="Add Farm Produce">
    <div class="max-w-lg bg-white dark:bg-zinc-900 p-6 rounded-xl border">
        <h1 class="text-xl font-semibold mb-4">Add Farm Produce</h1>

        <form
            method="POST"
            action="{{ route('manager.farm-produce.store') }}"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf

            {{-- Farmer --}}
            <div>
                <label class="block text-sm font-medium mb-1">Farmer</label>
                <select
                    name="farmer_id"
                    class="w-full border rounded-lg p-2"
                    required
                >
                    <option value="">Select farmer</option>
                    @foreach(\App\Models\Farmer::orderBy('name')->get() as $farmer)
                        <option
                            value="{{ $farmer->id }}"
                            {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}
                        >
                            {{ $farmer->name }} — {{ $farmer->municipality }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Product --}}
            <div>
                <label class="block text-sm font-medium mb-1">Product</label>
                <input
                    type="text"
                    name="product"
                    value="{{ old('product') }}"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea
                    name="description"
                    rows="3"
                    class="w-full border rounded-lg p-2"
                >{{ old('description') }}</textarea>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-medium mb-1">Quantity</label>
                <input
                    type="number"
                    name="quantity"
                    value="{{ old('quantity') }}"
                    min="0"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>

             {{-- Price --}}
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input
                    type="number"
                    name="price"
                    value="{{ old('price') }}"
                    min="0"
                    step="0.01"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>
                 
            

            {{-- Produce Image --}}
            <div>
                <label class="block text-sm font-medium mb-1">Produce Image</label>
                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="w-full text-sm"
                >
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-2">
                <a
                    href="{{ route('manager.farm-produce.index') }}"
                    class="px-4 py-2 border rounded-lg"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="bg-primary text-white px-4 py-2 rounded-lg"
                >
                    Save Produce
                </button>
            </div>
        </form>
    </div>
</x-layouts.mapp>
