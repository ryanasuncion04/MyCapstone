<x-layouts.mapp title="Edit Farm Produce">
    <div class="max-w-lg bg-white dark:bg-zinc-900 p-6 rounded-xl border">
        <h1 class="text-xl font-semibold mb-4">Edit Farm Produce</h1>

        <form
            method="POST"
            action="{{ route('manager.farm-produce.update', $farmProduce) }}"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

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
                            {{ old('farmer_id', $farmProduce->farmer_id) == $farmer->id ? 'selected' : '' }}
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
                    value="{{ old('product', $farmProduce->product) }}"
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
                >{{ old('description', $farmProduce->description) }}</textarea>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-medium mb-1">Quantity</label>
                <input
                    type="number"
                    name="quantity"
                    value="{{ old('quantity', $farmProduce->quantity) }}"
                    min="0"
                    class="w-full border rounded-lg p-2"
                    required
                >
            </div>

            {{-- Produce Image --}}
            <div>
                <label class="block text-sm font-medium mb-1">Produce Image</label>

                @if ($farmProduce->image)
                    <div class="mb-2">
                        <img
                            src="{{ asset('storage/' . $farmProduce->image) }}"
                            alt="{{ $farmProduce->product }}"
                            class="h-24 w-24 object-cover rounded border"
                        >
                    </div>
                @endif

                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="w-full text-sm"
                >

                <p class="text-xs text-zinc-500 mt-1">
                    Leave empty to keep current image
                </p>
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
                    Update Produce
                </button>
            </div>
        </form>
    </div>
</x-layouts.mapp>
