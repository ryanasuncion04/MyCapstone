<?php if (!isset($_instance)) { $_instance = null; } ?>

@props([
    'on',
])

@php
    $xInit = "console.warn('Livewire not available for action-message')";
    if (isset($_instance) && $_instance) {
        $xInit = "@this.on('" . e($on) . "', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })";
    }
@endphp

<div
    x-data="{ shown: false, timeout: null }"
    x-init="{!! $xInit !!}"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none"
    {{ $attributes->merge(['class' => 'text-sm']) }}
>
    {{ $slot->isEmpty() ? __('Saved.') : $slot }}
</div>
