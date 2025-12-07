@props([
    'title' => null,
    'subtitle' => null,
    'action' => '#',
    'method' => 'POST',
    'fields' => [],
    'data' => [],
    'submitText' => 'Guardar',
    'submitColor' => 'teal',
    'cancelText' => null,
    'cancelRoute' => null,
    'enctype' => null,
    'disabled' => false,
])

@php
    // Extraer todas las condiciones únicas de todos los campos (incluidos sub-formularios)
    $conditions = [];
    
    function extractConditions($fields, &$conditions) {
        foreach ($fields as $field) {
            if (isset($field['condition']) && !in_array($field['condition'], $conditions)) {
                $conditions[] = [
                    'key' => $field['condition'], 
                    'value' => $field['conditionDefault'] ?? true
                ];
            }
            if (isset($field['type']) && $field['type'] === 'group' && isset($field['fields'])) {
                extractConditions($field['fields'], $conditions);
            }
        }
    }
    
    extractConditions($fields, $conditions);
    
    // NUEVO: Mapear datos del servidor a los campos
    /* function mapDataToFields(&$fields, $data, $parentKey = '') {
        foreach ($fields as &$field) {
            if ($field['type'] === 'group') {
                // Para grupos, buscar en data usando el nombre del grupo
                $groupName = $field['name'] ?? null;
                $groupData = $groupName && isset($data[$groupName]) ? $data[$groupName] : $data;
                
                if (isset($field['fields'])) {
                    mapDataToFields($field['fields'], $groupData, $groupName);
                }
            } else {
                $fieldName = $field['name'] ?? null;
                
                if ($fieldName) {
                    // Si el campo ya tiene un 'value', respetarlo (override manual)
                    if (!isset($field['val']) && isset($data[$fieldName])) {
                        $field['val'] = $data[$fieldName];
                    }
                    
                    // Para checkboxes, usar 'checked' en lugar de 'value'
                    if ($field['type'] === 'checkbox') {
                        if (!isset($field['checked']) && isset($data[$fieldName])) {
                            $field['checked'] = (bool) $data[$fieldName];
                        }
                    }
                }
            }
        }
    } */
    // NUEVO: Mapear datos del servidor a los campos
    function mapDataToFields(&$fields, $data, $parentKey = '') {
        foreach ($fields as &$field) {
            if ($field['type'] === 'group') {
                // Para grupos, buscar en data usando el nombre del grupo
                $groupName = $field['name'] ?? null;
                $groupData = $groupName && isset($data[$groupName]) ? $data[$groupName] : $data;
                
                if (isset($field['fields'])) {
                    mapDataToFields($field['fields'], $groupData, $groupName);
                }
            } else {
                $fieldName = $field['name'] ?? null;
                
                if ($fieldName) {
                    // Si el campo ya tiene un 'value', respetarlo (override manual)
                    if (!isset($field['val']) && isset($data[$fieldName])) {
                        // Para multi-select y checkbox-group, necesitamos manejar arrays anidados
                        if (in_array($field['type'] ?? '', ['multi-select', 'checkbox-group'])) {
                            // Si el dato es un array de objetos (como specialties), extraer solo los IDs
                            if (is_array($data[$fieldName]) && !empty($data[$fieldName])) {
                                $firstItem = reset($data[$fieldName]);
                                if (is_array($firstItem) || is_object($firstItem)) {
                                    // Es un array de objetos, extraer el optValue
                                    $optValue = $field['optValue'] ?? 'id';
                                    $field['val'] = array_map(function($item) use ($optValue) {
                                        return is_array($item) ? $item[$optValue] : (is_object($item) ? $item->$optValue : $item);
                                    }, $data[$fieldName]);
                                } else {
                                    // Es un array simple de valores
                                    $field['val'] = $data[$fieldName];
                                }
                            } else {
                                $field['val'] = is_array($data[$fieldName]) ? $data[$fieldName] : [];
                            }
                        } else {
                            $field['val'] = $data[$fieldName];
                        }
                    }
                    
                    // Para checkboxes simples, usar 'checked' en lugar de 'value'
                    if ($field['type'] === 'checkbox') {
                        if (!isset($field['checked']) && isset($data[$fieldName])) {
                            $field['checked'] = (bool) $data[$fieldName];
                        }
                    }
                }
            }
        }
    }

    // Aplicar mapeo de datos
    if (!empty($data)) {
        mapDataToFields($fields, $data);
    }
    
    // NUEVO: Evaluar condiciones basadas en datos del servidor
    $alpineData = [];
    
    // Evaluar cada condición según los datos
    foreach ($conditions as $condition) {
        $key = $condition['key'];
        $defaultValue = $condition['value'];
        
        // Intentar evaluar la condición con los datos del servidor
        $evaluatedValue = $defaultValue;
        
        // Casos especiales para roles
        if (isset($data['role'])) {
            $roles = is_array($data['role']) ? $data['role'] : [$data['role']];
            
            // Evaluar condiciones comunes
            if ($key === 'isAdmin') {
                $evaluatedValue = in_array('ADMIN', $roles);
            } elseif ($key === 'isProfessional') {
                $evaluatedValue = in_array('DOCTOR', $roles);
            } elseif ($key === 'isPatient') {
                $evaluatedValue = in_array('PATIENT', $roles);
            }
        }
        
        $alpineData[$key] = $evaluatedValue ? 'true' : 'false';
    }

    // Colores de botón submit
    $submitColors = [
        'teal' => 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500',
        'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'purple' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
        'orange' => 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
        'gray' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
    ];
    
    $submitColorClass = $submitColors[$submitColor] ?? $submitColors['teal'];
    
    // Determinar si necesitamos @method
    $needsMethodField = !in_array(strtoupper($method), ['GET', 'POST']);
    $formMethod = $needsMethodField ? 'POST' : strtoupper($method);
@endphp

<div 
    x-data="{
        @foreach($alpineData as $key => $value)
            {{ $key }}: {{ $value }},
        @endforeach
        formData: {},
        submitForm() {
            console.log('Form submitted:', this.formData);
        }
    }"
    class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <!-- Header del formulario -->
    @if($title || $subtitle)
        <div class="px-6 py-5 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-gray-200">
            @if($title)
                <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <!-- Formulario -->
    <form x-on:submit="isLoading = true" action="{{ $action }}" 
        method="{{ $formMethod }}"
        @if($enctype) enctype="{{ $enctype }}" @endif
        class="p-6">
        
        @csrf
        
        @if($needsMethodField)
            @method($method)
        @endif

        <div class="space-y-6">
            @foreach($fields as $field)
                    <x-dynamic-form-field :field="$field" :disabled="$disabled" />
            @endforeach
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            @if($cancelText && $cancelRoute)
                <a 
                    href="{{ $cancelRoute }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    {{ $cancelText }}
                </a>
            @endif

            @if (!$disabled)
                <button                    
                    type="submit"
                    class="px-6 py-3 {{ $submitColorClass }} text-white font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-md hover:shadow-lg"
                >
                    {{ $submitText }}
                </button>
            @endif
        </div>
    </form>

    <x-script-refresh-token />
</div>