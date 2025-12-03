@props([
    'icon',
    'label',
    'route' => '#',
    'hasSubmenu' => false,
    'active' => false
])

@php
    $isActive = $active || request()->routeIs($route . '*');
    
    $icons = [
        'professionals' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'patients' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    ];
@endphp

<div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
    <a 
        href="{{ $hasSubmenu ? '#' : route($route) }}"
        @if($hasSubmenu) @click.prevent="open = !open" @endif
        class="flex items-center justify-between px-3 py-3 rounded-lg transition-all duration-200 group {{ $isActive ? 'bg-white/20 text-white shadow-lg' : 'text-teal-50 hover:bg-white/10 hover:text-white' }}"
    >
        <div class="flex items-center space-x-3">
            <svg class="w-5 h-5 {{ $isActive ? 'text-white' : 'text-teal-100 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$icon] ?? $icons['users'] !!}
            </svg>
            <span class="font-medium">{{ $label }}</span>
        </div>
        
        @if($hasSubmenu)
            <svg 
                :class="{ 'rotate-180': open }"
                class="w-4 h-4 transition-transform duration-200 {{ $isActive ? 'text-white' : 'text-teal-100' }}" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        @endif
    </a>
    
    @if($hasSubmenu)
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="mt-1 ml-8 space-y-1"
        >
            {{ $slot }}
        </div>
    @endif
</div>