@props([
    'columns' => [],
    'data' => [],
    'emptyMessage' => 'No hay registros para mostrar',
    'error' => false,
    'striped' => true,
    'hoverable' => true,
])

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <!-- Header -->
            <thead class="bg-gradient-to-r from-teal-50 to-emerald-50" style="background-color:#4B4B4B">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" style="background-color:#4B4B4B; text-align: center;" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider {{ $column['headerClass'] ?? '' }}">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Body -->
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $index => $row)
                    <tr class="
                        {{ $striped && $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}
                        {{ $hoverable ? 'hover:bg-teal-50' : '' }}
                        transition-colors duration-150
                    ">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap {{ $column['cellClass'] ?? '' }}">
                                @if(isset($column['component']))
                                    {{-- Renderizar componente personalizado --}}
                                    <x-dynamic-component 
                                        :component="$column['component']" 
                                        :row="$row"
                                        :value="data_get($row, $column['field'])"
                                        :column="$column"
                                    />
                                @elseif(isset($column['render']))
                                    {{-- Renderizar con callback --}}
                                    {!! $column['render']($row) !!}
                                @else
                                    {{-- Renderizar valor directo --}}
                                    <div class="text-sm text-gray-900">
                                        {{ data_get($row, $column['field']) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                @if ($error)
                                    <p class="text-sm font-medium text-red-400">{{ $emptyMessage }}</p>                                    
                                @else
                                    <p class="text-sm font-medium">{{ $emptyMessage }}</p>                   
                                @endif

                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>