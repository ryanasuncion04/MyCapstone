<x-layouts.app>
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Appearance</h1>

        @if(session('status') === 'appearance-updated')
            <div class="mb-4 text-green-600">Appearance settings updated.</div>
        @endif

        <form method="POST" action="{{ route('appearance.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Theme</label>
                <select name="theme" class="w-full border rounded px-3 py-2 dark:bg-zinc-800 dark:border-zinc-600">
                    <option value="light" {{ (\App\Models\Setting::get('theme') === 'light') ? 'selected' : '' }}>Light</option>
                    <option value="dark" {{ (\App\Models\Setting::get('theme') === 'dark') ? 'selected' : '' }}>Dark</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Primary Color</label>
                <input type="color" name="primary_color" value="{{ \App\Models\Setting::get('primary_color', '#0ea5e9') }}" class="w-20 h-10 p-0 border-0" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Layout</label>
                <select name="layout" class="w-full border rounded px-3 py-2 dark:bg-zinc-800 dark:border-zinc-600">
                    <option value="comfortable" {{ (\App\Models\Setting::get('layout') === 'comfortable') ? 'selected' : '' }}>Comfortable</option>
                    <option value="compact" {{ (\App\Models\Setting::get('layout') === 'compact') ? 'selected' : '' }}>Compact</option>
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</x-layouts.app>
