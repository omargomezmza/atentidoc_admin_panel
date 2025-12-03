@props([
    'name',
    'label',
    'checked' => false
])

<div class="flex items-center">
    <input 
        type="checkbox"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer']) }}
    >
    <label for="{{ $name }}" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">
        {{ $label }}
    </label>
</div>