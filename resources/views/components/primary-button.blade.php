@props([
    'type' => 'submit',
])

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) }}
>
    {{ $slot }}
</button>