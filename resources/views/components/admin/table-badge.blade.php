@props([
    'row',
    'value',
    'column' => [],
])

@php
    $colorMap = $column['colorMap'] ?? [
        'active' => 'green',
        'inactive' => 'gray',
        'pending' => 'yellow',
        'approved' => 'blue',
        'rejected' => 'red',
    ];
    
    $labelMap = $column['labelMap'] ?? [];
    
    $color = $colorMap[$value] ?? 'gray';
    $label = $labelMap[$value] ?? ucfirst($value);
    
    $colorClasses = [
        'green' => 'bg-green-100 text-green-800',
        'gray' => 'bg-gray-100 text-gray-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'red' => 'bg-red-100 text-red-800',
        'teal' => 'bg-teal-100 text-teal-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'orange' => 'bg-orange-100 text-orange-800',
    ];
@endphp

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $colorClasses[$color] ?? $colorClasses['gray'] }}">
    {{ $label }}
</span>