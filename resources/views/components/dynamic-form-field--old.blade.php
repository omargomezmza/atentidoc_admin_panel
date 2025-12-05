@props([
    'field' => [],
])

@php
    $type = $field['type'] ?? 'text';
    $name = $field['name'] ?? '';
    $label = $field['label'] ?? ucfirst($name);
    $placeholder = $field['placeholder'] ?? '';
    $required = $field['required'] ?? false;
    $disabled = $field['disabled'] ?? false;
    $readonly = $field['readonly'] ?? false;
    $value = $field['value'] ?? '';
    $options = $field['options'] ?? [];
    $optValue = $field['optValue'] ?? null;
    $optLabel = $field['optLabel'] ?? null;
    $condition = $field['condition'] ?? null;
    $action = $field['action'] ?? null;
    $helperText = $field['helperText'] ?? null;
    $rows = $field['rows'] ?? 3;
    $min = $field['min'] ?? null;
    $max = $field['max'] ?? null;
    $step = $field['step'] ?? null;
    $accept = $field['accept'] ?? null;
    $multiple = $field['multiple'] ?? false;
    $checked = $field['checked'] ?? false;
    
    // DATO DE SERVIDOR 
    $data = $field['val'] ?? '';

    // Atributos comunes
    $commonAttributes = [
        'id' => $name,
        'name' => $name,
        'class' => 'w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 text-gray-700',
    ];
    
    if ($placeholder) $commonAttributes['placeholder'] = $placeholder;
    if ($required) $commonAttributes['required'] = true;
    if ($disabled) $commonAttributes['disabled'] = true;
    if ($readonly) $commonAttributes['readonly'] = true;
    if ($value) $commonAttributes['value'] = $value;
    if ($min !== null) $commonAttributes['min'] = $min;
    if ($max !== null) $commonAttributes['max'] = $max;
    if ($step !== null) $commonAttributes['step'] = $step;
    if ($accept) $commonAttributes['accept'] = $accept;
    if ($multiple) $commonAttributes['multiple'] = true;
    
    // Alpine directives
    $alpineDirectives = '';
    if ($condition) {
        $alpineDirectives .= " x-show=\"{$condition}\"";
    }
    if ($action) {
        $alpineDirectives .= " @change=\"{$action}\"";
    }
    
    // Construir string de atributos
    $attributesString = '';
    foreach ($commonAttributes as $key => $val) {
        if (is_bool($val)) {
            $attributesString .= $val ? " {$key}" : '';
        } else {
            $attributesString .= " {$key}=\"" . e($val) . "\"";
        }
    }
@endphp

@if($type === 'group')
    {{-- Sub-formulario / Grupo de campos --}}
    <div 
        {!! $alpineDirectives !!}
        class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
        @if(isset($field['groupTitle']) || isset($field['groupSubtitle']))
            <div class="mb-6 pb-4 border-b border-gray-300">
                @if(isset($field['groupTitle']))
                    <h3 class="text-xl font-bold text-gray-800">{{ $field['groupTitle'] }}</h3>
                @endif
                @if(isset($field['groupSubtitle']))
                    <p class="text-sm text-gray-600 mt-1">{{ $field['groupSubtitle'] }}</p>
                @endif
            </div>
        @endif

        <div class="space-y-5">
            @foreach($field['fields'] ?? [] as $subField)
                <x-dynamic-form-field :field="$subField" />
            @endforeach
        </div>
    </div>

@elseif($type === 'textarea')
    {{-- Campo Textarea --}}
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <textarea 
            {!! $attributesString !!}
            rows="{{ $rows }}"
        >{{ $data }}</textarea>
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'select')
    {{-- Campo Select --}}
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <select {!! $attributesString !!}
            @if (isset($data))
                value="{{ is_array($data) ? array_shift($data) : $data }}"                
            @endif>
            <option value="">{{ $placeholder ?: 'Seleccione una opción' }}</option>
            @foreach($options as $optValue => $optLabel)
                <option 
                    value="{{ $optValue }}" 
                    {{ old($name, $value) == $optValue ? 'selected' : '' }}
                >
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'select-2')
    {{-- Campo Select version 2--}}
    {{-- 
        $optValue = $field['optValue'] ?? null;
        $optLabel = $field['optLabel'] ?? null;    
    --}}
    <div {!! $alpineDirectives !!} x-init="console.log('isProfessional', isProfessional)">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <select {!! $attributesString !!}>
            <option value="">{{ $placeholder ?: 'Seleccione una opción' }}</option>
            @foreach($options as $opt)
                <option 
                    value="{{ $opt[$optValue] }}" 
                    {{ old($name, $value) == $opt[$optValue] ? 'selected' : '' }}>

                    {{ $opt[$optLabel] }}

                </option>
            @endforeach
        </select>
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'checkbox')
    {{-- Campo Checkbox --}}
    <div {!! $alpineDirectives !!} class="flex items-start">
        <div class="flex items-center h-5">
            <input 
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                value="1"
                {{ old($name, $checked) ? 'checked' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                @if($action) @change="{{ $action }}" @endif
                class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2"
            >
        </div>
        <div class="ml-3">
            <label for="{{ $name }}" class="text-sm font-medium text-gray-700 cursor-pointer">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            @if($helperText)
                <p class="text-xs text-gray-500 mt-1">{{ $helperText }}</p>
            @endif
        </div>
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'radio')
    {{-- Campo Radio --}}
    <div {!! $alpineDirectives !!}>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <div class="space-y-2">
            @foreach($options as $optValue => $optLabel)
                <div class="flex items-center">
                    <input 
                        type="radio"
                        id="{{ $name }}_{{ $optValue }}"
                        name="{{ $name }}"
                        value="{{ $optValue }}"
                        {{ old($name, $value) == $optValue ? 'checked' : '' }}
                        {{ $required ? 'required' : '' }}
                        {{ $disabled ? 'disabled' : '' }}
                        @if($action) @change="{{ $action }}" @endif
                        class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 focus:ring-teal-500 focus:ring-2"
                    >
                    <label for="{{ $name }}_{{ $optValue }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                        {{ $optLabel }}
                    </label>
                </div>
            @endforeach
        </div>
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'file')
    {{-- Campo File --}}
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input 
            type="file"
            {!! $attributesString !!}
            class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
        >
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif(in_array($type, ['password']))
    {{-- Campo Password con toggle --}}
    <div {!! $alpineDirectives !!} x-data="{ showPassword: false }">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <div class="relative">
            <input 
                :type="showPassword ? 'text' : 'password'"
                {!! $attributesString !!}
            >
            <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
            >
                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@else
    {{-- Campos de input estándar (text, email, number, date, etc.) --}}
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <input 
            @if (isset($data))
                value="{{ $data }}"                
            @endif
            type="{{ $type }}"
            {!! $attributesString !!}
        >
        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
@endif