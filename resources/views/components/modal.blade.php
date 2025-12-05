@props([
    'name' => 'modal',
    'title' => null,
    'size' => 'md',
    'closeable' => true,
    'show' => false,
    'maxWidth' => null,
])

@php
    // Definir tamaños predefinidos
    $sizeClasses = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full',
    ];
    
    $maxWidthClass = $maxWidth ? $maxWidth : ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<div
    x-data="{
        show: @js($show),
        name: '{{ $name }}',
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...this.$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
        close() {
            this.show = false
            this.$dispatch('modal-closed', { name: this.name })
        },
        open() {
            this.show = true
            this.$dispatch('modal-opened', { name: this.name })
            this.$nextTick(() => this.firstFocusable()?.focus())
        }
    }"
    x-on:open-modal.window="if ($event.detail.name === name) open()"
    x-on:close-modal.window="if ($event.detail.name === name) close()"
    x-on:keydown.escape.window="if (show) close()"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;"
>
    <!-- Overlay/Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transform transition-all"
        @click="close()"
    ></div>

    <!-- Modal Container -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full {{ $maxWidthClass }} mx-auto"
    >
        <!-- Modal Content -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            @if($title || $closeable)
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-gray-200">
                    @if($title)
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ $title }}
                        </h3>
                    @else
                        <div></div>
                    @endif

                    @if($closeable)
                        <button
                            type="button"
                            @click="close()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150 p-1 rounded-lg hover:bg-white/50"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif

            <!-- Body -->
            <div class="px-6 py-6">
                {{ $slot }}
            </div>

            <!-- Footer (opcional) -->
            @if(isset($footer))
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>