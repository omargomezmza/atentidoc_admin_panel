@props([
    'row',
    'value' => null,
    'column' => [],
    'editRoute' => null,
    'deleteRoute' => null,
    'viewRoute' => null,
    'customActions' => [],
    'viewTarget' => null,
    'editTarget' => null,
    'deleteTarget' => null,
])

@php
    // Obtener rutas desde la columna si existen
    $editRoute = $editRoute ?? ($column['editRoute'] ?? null);
    $deleteRoute = $deleteRoute ?? ($column['deleteRoute'] ?? null);
    $viewRoute = $viewRoute ?? ($column['viewRoute'] ?? null);
    $customActions = $customActions ?: ($column['actions'] ?? []);

    $editTarget = $editTarget ?? ($column['editTarget'] ?? null);
    $deleteTarget = $deleteTarget ?? ($column['deleteTarget'] ?? null);
    $viewTarget = $viewTarget ?? ($column['viewTarget'] ?? null);
@endphp

<div class="flex items-center space-x-2">
    
    {{-- Ver --}}
    @if($viewRoute)
        <a {{-- @click="$dispatch('open-modal', { name: '{{ $viewTarget }}' })" --}}
            href="{{ is_callable($viewRoute) ? $viewRoute($row) : $viewRoute }}" 
            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150"
            title="Ver detalles">

            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </a>
    @endif

    {{-- Editar --}}
    @if($editRoute)
        <a 
            href="{{ is_callable($editRoute) ? $editRoute($row) : $editRoute }}" 
            class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors duration-150"
            title="Editar"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    @endif

    {{-- Eliminar --}}

    {{-- 
         
                + JSON.stringify('{{ $row }}') 
                + '  -   ' +  JSON.stringify('{{ $value }}'));
    --}}
    @if($deleteRoute)
        {{-- <form action="{{ is_callable($deleteRoute) ? $deleteRoute($row) : $deleteRoute }}" 
            method="POST" 
            class="inline-block"
            
            onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro? ');">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg 
                transition-colors duration-150"
                title="Eliminar">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                
            </button>
        </form> --}}

        
        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg 
            transition-colors duration-150"
            title="Eliminar" x-on:click="in_process = {{ $value }}; 
                $dispatch('open-modal', { name: '{{ $viewTarget }}' }); 
                console.log('SE HIZO CLICK EN ELIMINAR', '{{ $value }}')">

            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            
        </button>
    
    @endif

    {{-- Acciones personalizadas --}}
    @foreach($customActions as $action)
        @if(isset($action['component']))
            <x-dynamic-component :component="$action['component']" :row="$row" />
        @else
            <a 
                href="{{ is_callable($action['url']) ? $action['url']($row) : $action['url'] }}" 
                class="p-2 {{ $action['class'] ?? 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition-colors duration-150"
                title="{{ $action['title'] ?? '' }}"
            >
                @if(isset($action['icon']))
                    {!! $action['icon'] !!}
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                @endif
            </a>
        @endif
    @endforeach

</div>