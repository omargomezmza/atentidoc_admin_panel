@props([
    'target' => 'modal',
    'variant' => 'primary',
])

@php
    $variantClasses = [
        'primary' => 'bg-teal-600 hover:bg-teal-700 text-white',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'info' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'outline' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
    ];
    
    $classes = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<button
    type="button"
    @click="$dispatch('open-modal', { name: '{{ $target }}' })"
    {{ $attributes->merge(['class' => "px-4 py-2 rounded-lg font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 {$classes}"]) }}
>
    {{ $slot }}
</button>