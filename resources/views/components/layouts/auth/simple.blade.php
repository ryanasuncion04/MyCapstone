<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-cream-50 via-white to-primary-50 antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('welcome') }}" class="flex flex-col items-center gap-2 font-medium hover:opacity-80 transition-opacity" wire:navigate>
                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 shadow-md">
                        <x-app-logo-icon class="size-8 fill-current text-white" />
                    </span>
                    <span class="text-sm font-semibold text-primary-700">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6 mt-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
