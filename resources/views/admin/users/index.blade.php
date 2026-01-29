<x-layouts.adapp title="Users">
    <div class="max-w-6xl bg-white dark:bg-zinc-900 p-6 rounded-xl border">
        <h1 class="text-xl font-semibold mb-4">Users</h1>

        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Email</th>
                    <th class="text-left py-2">Municipality</th>
                    <th class="text-left py-2">Role</th>
                    <th class="text-right py-2">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                    <tr class="border-b">
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2 text-sm text-zinc-500">{{ $user->email }}</td>
                        <td class="py-2">
                            @if (auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.update-municipality', $user) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="municipality" class="border rounded-lg p-1 text-sm w-full"
                                        onchange="this.form.submit()">
                                        <option value="">—</option>

                                        @foreach ($municipalities as $municipality)
                                            <option value="{{ $municipality }}" @selected($user->municipality === $municipality)>
                                                {{ $municipality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="text-xs text-zinc-400">
                                    {{ $user->municipality ?? '—' }}
                                </span>
                            @endif
                        </td>

                        {{-- ROLE --}}
                        <td class="py-2">
                            <span class="px-2 py-1 text-xs rounded-lg border">
                                {{ ucfirst($user->role->value) }}
                            </span>
                        </td>

                        {{-- ASSIGN ROLE --}}
                        <td class="py-2 text-right">
                            @if (auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                                    class="flex justify-end gap-2">
                                    @csrf
                                    @method('PATCH')

                                    @php
                                        $roles = ['user', 'manager', 'admin'];
                                    @endphp

                                    <select name="role" class="border rounded-lg p-1 text-sm"
                                        onchange="this.form.submit()">
                                        <option disabled>Change role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected($user->role->value === $role)>
                                                {{ ucfirst($role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="text-xs text-zinc-400">You</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-zinc-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.adapp>
