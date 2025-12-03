@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'blue',
    'link' => null
])

@php
    $colorClasses = [
        'blue' => 'from-blue-500 to-blue-600',
        'teal' => 'from-teal-500 to-emerald-600',
        'purple' => 'from-purple-500 to-purple-600',
        'orange' => 'from-orange-500 to-orange-600',
        'pink' => 'from-pink-500 to-pink-600',
    ];
    
    $gradientClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
    <div class="bg-gradient-to-br {{ $gradientClass }} p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-white/80 text-sm font-medium mb-1">{{ $title }}</p>
                <p class="text-white text-4xl font-bold">{{ $value }}</p>
            </div>
            
            @if($icon)
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $icon !!}
                    </svg>
                </div>
            @endif
        </div>
    </div>
    
    @if($link)
        <a href="{{ $link }}" class="block px-6 py-3 bg-gray-50 text-sm font-medium text-gray-600 hover:text-{{ $color }}-600 hover:bg-gray-100 transition-colors duration-200">
            Ver detalles →
        </a>
    @endif
</div>