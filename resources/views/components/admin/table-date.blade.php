@props([
    'row',
    'value',
    'column' => [],
])

@php
    $format = $column['format'] ?? 'd/m/Y H:i';
    $showRelative = $column['showRelative'] ?? true;
    
    try {
        $date = $value instanceof \Carbon\Carbon ? $value : \Carbon\Carbon::parse($value);
        $formatted = $date->format($format);
        $relative = $date->diffForHumans();
    } catch (\Exception $e) {
        $formatted = $value;
        $relative = null;
    }
@endphp

<div class="text-sm" style="text-align: center;">
    <div class="text-gray-900">{{ $formatted }}</div>
    @if($showRelative && $relative)
        <div class="text-xs text-gray-500">{{ $relative }}</div>
    @endif
</div>