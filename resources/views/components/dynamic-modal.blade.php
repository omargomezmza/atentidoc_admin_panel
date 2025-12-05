@props([
    'name' => 'modal',
    'title' => null,
    'size' => 'md',
    'closeable' => true,
    'show' => false,
    'component' => null,
    'componentData' => [],
])

<x-modal 
    :name="$name"
    :title="$title"
    :size="$size"
    :closeable="$closeable"
    :show="$show"
>
    @if($component)
        <x-dynamic-component 
            :component="$component" 
            :data="$componentData"
            v-bind="$componentData"
        />
    @else
        {{ $slot }}
    @endif

    @if(isset($footer))
        <x-slot:footer>
            {{ $footer }}
        </x-slot:footer>
    @endif
</x-modal>