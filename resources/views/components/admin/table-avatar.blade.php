@props([
    'row',
    'value',
    'column' => [],
])

@php
    //dd($column, $row, $value);
    $name = $column['nameField'] ? data_get($row, $column['nameField']) : $value;
    $image = isset($column['imageField']) ? data_get($row, $column['imageField']) : null;
    $subtitle = isset($column['subtitleField']) ? data_get($row, $column['subtitleField']) : null;
@endphp

<div class="flex items-center">
    <div class="flex-shrink-0 h-10 w-10">
        @if($image)
            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white" src="{{ $image }}" alt="{{ $name }}">
        @else
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center ring-2 ring-white">
                <span class="text-sm font-semibold text-white">
                    {{ strtoupper(substr($name, 0, 2)) }}
                </span>
            </div>
        @endif
    </div>
    <div class="ml-3">
        <div class="text-sm font-medium text-gray-900">
            {{ $name }}
        </div>
        @if($subtitle)
            <div class="text-xs text-gray-500">
                {{ $subtitle }}
            </div>
        @endif
    </div>
</div>