<x-layouts.app>
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Change Password</h1>

        @if(session('status') === 'password-updated')
            <div class="mb-4 text-green-600">Password updated.</div>
        @endif

        @if($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('user-password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full border rounded px-3 py-2" required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required />
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</x-layouts.app>
