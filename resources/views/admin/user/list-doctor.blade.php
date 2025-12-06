@extends('layouts.admin')

@section('title', 'Panel de Administrador')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    'in_process':null,
    async deleting() {
        const URI = '{{ route("admin.delete.user") }}/' + this.in_process;
        console.log('DELETING', URI);
        try {
            const res = await axios.delete(URI);
            console.log('data server', res.data);
            if (res.data.status === 'OK') {
                alert('El usuario fue eliminado con éxito.');
                window.location.reload();
            }
            else if (res.data.msg) {
                alert('Ocurrió un error:  [ ' + res.data.msg + ' ]');
            }
            else {
                alert('Ocurrió un error durante el proceso de eliminación de usuario. Intentelo de nuevo más tarde.');
            }
        }
        catch (e) {
            alert('Ocurrió un error durante el proceso de eliminación de usuario. Intentelo de nuevo más tarde.');
            console.log('ERROR: ', e);
        }
    }
}">
    
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Lista de Doctores</h1>
        <p class="text-gray-600">Puede leer, actualizar y eliminar esta información</p>
    </div>
    
    <!-- Search Box -->
    <div class="mb-8">
        <x-admin.search-box placeholder="Buscar en el panel..." />
    </div>

    {{-- Botón de Creación --}}
    <a class="px-6 py-3 bg-green-200 text-gray-700 font-medium rounded-lg hover:bg-green-300 
        transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 
        focus:ring-green-500"
        href="{{ route('admin.create.user') }}">
        Crear Usuario
    </a>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- <x-admin.stat-card 
            title="Solicitudes"
            value="10"
            color="blue"
            link="#"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </x-slot:icon>
        </x-admin.stat-card> --}}
        
        {{-- <x-admin.stat-card 
            title="Profesionales"
            value="25"
            color="teal"
            link="#"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </x-slot:icon>
        </x-admin.stat-card> --}}
        
        {{-- <x-admin.stat-card 
            title="Pacientes"
            value="150"
            color="purple"
            link="#"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </x-slot:icon>
        </x-admin.stat-card> --}}
        
    </div>
    
    <!-- Additional Content Area -->
    {{-- <div class="bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Actividad Reciente</h2>
        <p class="text-gray-600">Aquí se mostrará la actividad reciente del sistema...</p>
    </div> --}}
    

    @php
        $content = array_map(fn ($el) => (object) $el, $list['content']); 
        //dd($list, $content);
    @endphp
    <x-admin.table 
        :columns="[
            [
                'label' => 'ID',
                'field' => 'id',
            ],
            [
                'label' => 'Nombre',
                'field' => 'firstName',
                'component' => 'admin.table-avatar',
                'nameField' => 'firstName',
                'subtitleField' => 'lastName',
            ],
            [
                'label' => 'Email',
                'field' => 'email',
            ],
            [
                'label' => 'Estado',
                'field' => 'status',
                'component' => 'admin.table-badge',
                'colorMap' => [
                    'pending' => 'yellow',
                    'ACTIVE' => 'green',
                    'rejected' => 'red',
                ],
                'labelMap' => [
                    'pending' => 'Pendiente',
                    'ACTIVE' => 'Aprobado',
                    'rejected' => 'Rechazado',
                ],
            ],
            [
                'label' => 'Fecha de Nacimiento',
                'field' => 'birthDate',
                'component' => 'admin.table-date',
                'format' => 'd/m/Y',
            ],
            [
                'label' => 'Género',
                'field' => 'gender',
                'component' => 'admin.table-badge',
                'labelMap' => [
                    'MALE' => 'Hombre',
                    'FEMALE' => 'Mujer',
                    'OTHER' => 'Otro',
                    null => '--',
                ],
            ],
            [
                'label' => 'Acciones',
                'field' => 'id',
                'component' => 'admin.table-actions',
                'viewRoute' => fn($row) => route('admin.show.user', $row->id),
                'editRoute' => fn($row) => route('admin.edit.user', $row->id),
                'deleteRoute' => fn($row) => '#', //route('admin.requests.destroy', $row->id),
                'viewTarget' => 'simple-modal',
                'deleteTarget'=> 'simple-modal',
            ],
        ]"
        :data="$content"
    />

    <!-- Botón para abrir -->
    {{-- <x-modal-trigger target="simple-modal">
        Abrir Modal Simple
    </x-modal-trigger> --}}

    <!-- Modal -->
    <x-modal name="simple-modal" title="¿Eliminar Usuario?" size="md">
        <div class="space-y-4">
            <p class="text-red-600">
                ¿Estás seguro de que desea ELIMINAR este usuario?
            </p>
            <p class="text-sm text-gray-500">
                Esta acción no se puede deshacer.
            </p>
        </div>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" @click="$dispatch('close-modal', { name: 'simple-modal' })" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 
                    transition-colors">
                    Cancelar
                </button>
                <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg 
                    hover:bg-red-700 transition-colors" x-on:click="deleting()">
                    ELIMINAR
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

</div>
@endsection