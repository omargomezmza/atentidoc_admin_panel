@props([
    'field' => [],
    'disabled' => false,
])

@php
    $type = $field['type'] ?? 'text';
    $name = $field['name'] ?? '';
    $label = $field['label'] ?? ucfirst($name);
    $placeholder = $field['placeholder'] ?? '';
    $required = $field['required'] ?? false;
    //$disabled = $field['disabled'] ?? false;
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
    // NO poner value aquí para inputs que no son select
    if ($value && !in_array($type, ['select', 'select-2', 'checkbox', 'radio'])) {
        $commonAttributes['value'] = $value;
    }
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
                <x-dynamic-form-field :field="$subField" :disabled="$disabled"  />
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
                @if ($disabled)
                    disabled
                @endif 
            {!! $attributesString !!}
            rows="{{ $rows }}"
        >{{ old($name, $data) }}</textarea>
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
                @if ($disabled)
                    disabled
                @endif  >
            <option value="">{{ $placeholder ?: 'Seleccione una opción' }}</option>
            @foreach($options as $optionValue => $optionLabel)
                @php
                    // Obtener el valor actual (puede venir de old, $data, o $value)
                    $currentValue = old($name, $data ?: $value);
                    // Si es array, tomar el primer elemento
                    if (is_array($currentValue)) {
                        $currentValue = reset($currentValue);
                    }
                @endphp
                <option 
                    value="{{ $optionValue }}" 
                    {{ $currentValue == $optionValue ? 'selected' : '' }}
                >
                    {{ $optionLabel }}
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
    {{-- Campo Select con estructura personalizada (id/name) --}}
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <select {!! $attributesString !!} 
                @if ($disabled)
                    disabled
                @endif >
            <option value="">{{ $placeholder ?: 'Seleccione una opción' }}</option>
            @foreach($options as $option)
                @php
                    // Obtener valor y label del objeto/array
                    $itemValue = is_array($option) ? $option[$optValue] : (is_object($option) ? $option->$optValue : $option);
                    $itemLabel = is_array($option) ? $option[$optLabel] : (is_object($option) ? $option->$optLabel : $option);
                    
                    // Obtener el valor actual
                    $currentValue = old($name, $data ?: $value);
                    // Si es array, tomar el primer elemento
                    if (is_array($currentValue)) {
                        $currentValue = reset($currentValue);
                    }
                @endphp
                <option 
                    value="{{ $itemValue }}" 
                    {{ $currentValue == $itemValue ? 'selected' : '' }}
                >
                    {{ $itemLabel }}
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
                @if ($disabled)
                    disabled
                @endif 
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                value="1"
                {{ old($name, $checked || $data) ? 'checked' : '' }}
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
            @foreach($options as $optionValue => $optionLabel)
                @php
                    $currentValue = old($name, $data ?: $value);
                @endphp
                <div class="flex items-center">
                    <input 
                        @if ($disabled)
                            disabled
                        @endif 
                        type="radio"
                        id="{{ $name }}_{{ $optionValue }}"
                        name="{{ $name }}"
                        value="{{ $optionValue }}"
                        {{ $currentValue == $optionValue ? 'checked' : '' }}
                        {{ $required ? 'required' : '' }}
                        {{ $disabled ? 'disabled' : '' }}
                        @if($action) @change="{{ $action }}" @endif
                        class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 focus:ring-teal-500 focus:ring-2"
                    >
                    <label for="{{ $name }}_{{ $optionValue }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                        {{ $optionLabel }}
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

{{-- @elseif($type === 'file')
    <div {!! $alpineDirectives !!}>
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        @if($data)
            <p class="text-xs text-gray-600 mb-2">
                Archivo actual: <span class="font-semibold">{{ basename($data) }}</span>
            </p>
        @endif
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
--}}
@elseif($type === 'file')
    {{-- Campo File con Preview y Manejo de Base64 --}}
    <div {!! $alpineDirectives !!}
        x-data="{
            fileData: '{{ $data }}',
            fileName: '{{ $data ? basename($data) : '' }}',
            filePreview: null,
            fileType: null,
            isImage: false,
            isPdf: false,
            base64Data: null,
            
            init() {
                // Detectar tipo de archivo existente
                if (this.fileData) {
                    this.detectFileType(this.fileData);
                }
            },
            
            detectFileType(data) {
                // Verificar si es una URL
                if (data.startsWith('http://') || data.startsWith('https://') || data.startsWith('/')) {
                    this.fileType = 'url';
                    // Detectar si es imagen por extensión
                    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
                    this.isImage = imageExtensions.some(ext => data.toLowerCase().includes(ext));
                    this.isPdf = data.toLowerCase().includes('.pdf');
                    if (this.isImage) {
                        this.filePreview = data;
                    }
                }
                // Verificar si es base64
                else if (data.startsWith('data:')) {
                    this.fileType = 'base64';
                    this.base64Data = data;
                    if (data.startsWith('data:image/')) {
                        this.isImage = true;
                        this.filePreview = data;
                    } else if (data.startsWith('data:application/pdf')) {
                        this.isPdf = true;
                    }
                }
                // Si no tiene protocolo, asumir que es una ruta relativa
                else if (data) {
                    this.fileType = 'url';
                    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
                    this.isImage = imageExtensions.some(ext => data.toLowerCase().includes(ext));
                    this.isPdf = data.toLowerCase().includes('.pdf');
                    if (this.isImage) {
                        this.filePreview = data;
                    }
                }
            },
            
            async handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) return;
                
                this.fileName = file.name;
                
                // Detectar tipo de archivo nuevo
                if (file.type.startsWith('image/')) {
                    this.isImage = true;
                    this.isPdf = false;
                    // Crear preview de imagen
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.filePreview = e.target.result;
                        this.base64Data = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    this.isImage = false;
                    this.isPdf = true;
                    this.filePreview = null;
                    // Convertir PDF a base64
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.base64Data = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.isImage = false;
                    this.isPdf = false;
                    this.filePreview = null;
                }
                
                this.fileType = 'new';
            },
            
            downloadPdf() {
                if (!this.base64Data) return;
                
                // Crear elemento anchor temporal para descargar
                const link = document.createElement('a');
                link.href = this.base64Data;
                link.download = this.fileName || 'documento.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            
            clearFile() {
                this.filePreview = null;
                this.fileName = '';
                this.fileData = '';
                this.base64Data = null;
                this.isImage = false;
                this.isPdf = false;
                this.fileType = null;
                // Limpiar input file
                this.$refs.fileInput.value = '';
            }
        }">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>

        {{-- Preview de Imagen --}}
        <div 
            x-show="isImage && filePreview"
            class="mb-4 relative group"
            style="display: none;">
            <div class="relative inline-block">
                <img 
                    :src="filePreview" 
                    :alt="fileName"
                    class="max-w-xs max-h-64 rounded-lg shadow-md border-2 border-gray-200">
                <!-- Overlay con opciones -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center">
                    <button
                        @if ($disabled)
                            disabled
                        @endif 
                        type="button"
                        @click="clearFile()"
                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
            <p class="text-xs text-gray-600 mt-2">
                <span x-text="fileName"></span>
            </p>
        </div>

        {{-- Info de PDF del Servidor (con descarga) --}}
        <div x-show="isPdf && fileType === 'base64' && base64Data"
            class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg"
            style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            ✓ Archivo cargado en el sistema
                        </p>
                        <p class="text-xs text-gray-600" x-text="fileName"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        type="button"
                        @click="downloadPdf()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Descargar</span>
                    </button>
                    <button
                        @if ($disabled)
                            disabled
                        @endif 
                        type="button"
                        @click="clearFile()"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

        {{-- Info de PDF Nuevo (solo nombre, sin descarga) --}}
        <div 
            x-show="isPdf && fileType === 'new'"
            class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg"
            style="display: none;"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            Nuevo archivo seleccionado
                        </p>
                        <p class="text-xs text-gray-600" x-text="fileName"></p>
                    </div>
                </div>
                <button
                    @if ($disabled)
                        disabled
                    @endif 
                    type="button"
                    @click="clearFile()"
                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors"
                >
                    Eliminar
                </button>
            </div>
        </div>

        {{-- Info de PDF desde URL (sin descarga desde aquí) --}}
        <div 
            x-show="isPdf && fileType === 'url'"
            class="mb-4 p-4 bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-200 rounded-lg"
            style="display: none;"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            ✓ Archivo cargado
                        </p>
                        <p class="text-xs text-gray-600" x-text="fileName"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    
                    <template x-if="false">
                        <a :href="fileData"
                            target="_blank"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            
                                <span>Ver</span>
                            
                        </a>
                    </template>
                    <button 
                        @if ($disabled)
                            disabled
                        @endif  
                        type="button"
                        @click="clearFile()"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

        {{-- Input File --}}
        <input 
            @if ($disabled)
                disabled
            @endif  
            type="file"
            x-ref="fileInput"
            @change="handleFileChange($event)"
            {!! $attributesString !!}
            class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
        >

        {{-- Hidden input para enviar base64 al servidor --}}
        <input 
            @if ($disabled)
                disabled
            @endif  
            x-show="base64Data"
            type="hidden" 
            :name="'{{ $name }}_base64'" 
            :value="base64Data"
            style="display: none;"
        >

        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
@elseif($type === 'multi-select')
    {{-- Campo Multi-Select con Chips --}}
    <div {!! $alpineDirectives !!} 
        x-data="{
            selectedItems: {{ json_encode(is_array($data) ? $data : []) }},
            availableOptions: {{ json_encode($options) }},
            optValue: '{{ $optValue }}',
            optLabel: '{{ $optLabel }}',
            showDropdown: false,
            searchQuery: '',
            
            get filteredOptions() {
                return this.availableOptions.filter(option => {
                    const label = this.getLabel(option).toLowerCase();
                    const isNotSelected = !this.isSelected(this.getValue(option));
                    const matchesSearch = label.includes(this.searchQuery.toLowerCase());
                    return isNotSelected && matchesSearch;
                });
            },
            
            getValue(option) {
                return typeof option === 'object' ? option[this.optValue] : option;
            },
            
            getLabel(option) {
                return typeof option === 'object' ? option[this.optLabel] : option;
            },
            
            isSelected(value) {
                return this.selectedItems.includes(value);
            },
            
            toggleOption(option) {
                const value = this.getValue(option);
                if (this.isSelected(value)) {
                    this.removeItem(value);
                } else {
                    this.selectedItems.push(value);
                }
                this.searchQuery = '';
            },
            
            removeItem(value) {
                this.selectedItems = this.selectedItems.filter(item => item !== value);
            },
            
            getSelectedLabel(value) {
                const option = this.availableOptions.find(opt => this.getValue(opt) === value);
                return option ? this.getLabel(option) : value;
            }
        }"
        @if (!$disabled)
            @click.away="showDropdown = false"
        @endif
        >
        
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>

        {{-- Hidden inputs para enviar los valores seleccionados --}}
        <template x-for="(item, index) in selectedItems" :key="index">
            <input
                @if ($disabled)
                    disabled
                @endif 
                type="hidden" :name="'{{ $name }}[' + index + ']'" :value="item">
        </template>

        {{-- Contenedor de chips y dropdown --}}
        <div class="relative">
            {{-- Área de chips y búsqueda --}}
            <div 
                @if (!$disabled)
                    @click="showDropdown = !showDropdown"
                @endif
                class="min-h-[48px] w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-teal-500 focus-within:border-transparent transition-all duration-200 cursor-text"
            >
                {{-- Chips seleccionados --}}
                <div class="flex flex-wrap gap-2 mb-2" x-show="selectedItems.length > 0">
                    <template x-for="item in selectedItems" :key="item">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-teal-500 to-emerald-500 text-white rounded-full text-sm font-medium shadow-sm">
                            <span x-text="getSelectedLabel(item)"></span>
                            <button 
                                type="button"
                                @if (!$disabled)
                                    @click.stop="removeItem(item)"
                                @endif
                                class="hover:bg-white/20 rounded-full p-0.5 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Input de búsqueda --}}
                <input 
                    @if ($disabled)
                        disabled
                    @endif
                    type="text"
                    x-model="searchQuery"
                    @focus="showDropdown = true"
                    @click.stop
                    :placeholder="selectedItems.length === 0 ? '{{ $placeholder ?: 'Seleccione opciones...' }}' : 'Buscar más opciones...'"
                    class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 text-gray-700 placeholder-gray-400 p-0"
                >
            </div>

            {{-- Dropdown de opciones --}}
            <div 
                x-show="showDropdown && filteredOptions.length > 0"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                style="display: none;">

                <template x-for="option in filteredOptions" :key="getValue(option)">
                    <div @click="toggleOption(option)"
                        class="px-4 py-3 hover:bg-teal-50 cursor-pointer transition-colors duration-150 border-b border-gray-100 last:border-0">

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700" x-text="getLabel(option)"></span>
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>

                    </div>
                </template>
            </div>

            {{-- Mensaje cuando no hay opciones --}}
            <div x-show="showDropdown && filteredOptions.length === 0"
                class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl p-4 text-center"
                style="display: none;">
                <p class="text-sm text-gray-500">No hay más opciones disponibles</p>
            </div>
        </div>

        @if($helperText)
            <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

@elseif($type === 'checkbox-group')
    {{-- Grupo de Checkboxes --}}
    <div {!! $alpineDirectives !!}
        x-data="{
            selectedValues: {{ json_encode(is_array($data) ? $data : []) }},
            
            toggleValue(value) {
                if (this.isChecked(value)) {
                    this.selectedValues = this.selectedValues.filter(v => v !== value);
                } else {
                    this.selectedValues.push(value);
                }
            },
            
            isChecked(value) {
                return this.selectedValues.includes(value);
            }
        }">
        <label class="block text-sm font-medium text-gray-700 mb-3">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>

        {{-- Hidden inputs para enviar los valores seleccionados --}}
        <template x-for="(value, index) in selectedValues" :key="index">
            <input 
                @if ($disabled)
                    disabled
                @endif 
                type="hidden" :name="'{{ $name }}[' + index + ']'" :value="value">
        </template>

        {{-- Grid de checkboxes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($options as $optionValue => $optionLabel)
                <div 
                    @if ($disabled)
                        disabled
                    @endif
                    @if (!$disabled)
                        @click="toggleValue('{{ $optionValue }}')"
                    @endif
                    :class="isChecked('{{ $optionValue }}') ? 'bg-gradient-to-r from-teal-50 to-emerald-50 border-teal-500' : 'bg-gray-50 border-gray-200 hover:border-teal-300'"
                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 group"
                >
                    <div class="flex items-center h-5">
                        <input 
                            @if ($disabled)
                                disabled
                            @endif
                            type="checkbox"
                            :id="'{{ $name }}_{{ $optionValue }}'"
                            value="{{ $optionValue }}"
                            :checked="isChecked('{{ $optionValue }}')"
                            @change="toggleValue('{{ $optionValue }}')"
                            @click.stop
                            class="w-5 h-5 text-teal-600 bg-white border-gray-300 rounded focus:ring-teal-500 focus:ring-2 cursor-pointer"
                        >
                    </div>
                    <div class="ml-3 flex-1">
                        <label 
                            :for="'{{ $name }}_{{ $optionValue }}'"
                            class="text-sm font-medium text-gray-700 cursor-pointer select-none"
                        >
                            {{ $optionLabel }}
                        </label>
                    </div>
                    {{-- Icono de check cuando está seleccionado --}}
                    <div 
                        x-show="isChecked('{{ $optionValue }}')"
                        class="absolute top-2 right-2"
                        style="display: none;"
                    >
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        @if($helperText)
            <p class="mt-2 text-xs text-gray-500">{{ $helperText }}</p>
        @endif
        @error($name)
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
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
                @if ($disabled)
                    disabled
                @endif 
                :type="showPassword ? 'text' : 'password'"
                {!! $attributesString !!}
            >
            <button 
                @if ($disabled)
                    disabled
                @endif
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
            @if ($disabled)
                disabled
            @endif
            type="{{ $type }}"
            value="{{ old($name, $data ?: $value) }}"
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