@extends('layouts.admin')

@section('title', 'Panel de Administrador')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    'in_process':null,
    async deleting() {
        const URI = '{{ route("admin.delete.user") }}/' + this.in_process;
        console.log('DELETING', URI);
        try {
            this.isLoading = true;
            const res = await axios.delete(URI);
            console.log('data server', res.data);
            if (res.data.status === 'OK') {
                //this.isLoading = false;
                //alert('El usuario fue eliminado con éxito.');
                window.location.reload();
                
            }
            else if (res.data.msg) {
                this.isLoading = false;
                //alert('Ocurrió un error:  [ ' + res.data.msg + ' ]');
                $dispatch('open-modal', { name: 'error-modal' }); 
                
            }
            else {
                this.isLoading = false;
                //alert('Ocurrió un error durante el proceso de eliminación de usuario. Intentelo de nuevo más tarde.');
                $dispatch('open-modal', { name: 'error-modal' }); 

            }
        }
        catch (e) {
            this.isLoading = false;
            $dispatch('open-modal', { name: 'error-modal' }); 
            //alert('Ocurrió un error durante el proceso de eliminación de usuario. Intentelo de nuevo más tarde.');
            console.log('ERROR: ', e);
        }
    }
}">
    
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Lista de Pacientes</h1>
        <p class="text-gray-600">Puede leer, actualizar y eliminar esta información</p>
    </div>

    <div class="flex items-center gap-4 mb-8">
        <!-- Search Box (ocupa todo el espacio posible) -->
        <div class="flex-1">
            <x-admin.search-box placeholder="Buscar en el panel..." />
        </div>

        <!-- Create Button -->
        <x-link-button />
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">    
    </div>
    
    @php
        $content = is_null($list) ? [] : array_map(fn ($el) => (object) $el, $list['content']); 
    @endphp
    <x-admin.table 
        :columns="[
            [
                'label' => 'ID',
                'field' => 'id',
            ],
            [
                'label' => 'Email',
                'field' => 'email',
            ],
            [
                'label' => 'Roles',
                'field' => 'role',
                'render' => fn ($field) => implode(' | ', array_map(
                        function ($role) { 
                            if ($role === 'PATIENT') {
                                return 'Paciente'; 
                            }
                            else if ($role === 'DOCTOR') {
                                return 'Doctor';
                            } 
                            else if ($role === 'ADMIN') {
                                return 'Admin';
                            } 
                            else {
                                return $role;
                            }
                        }, $field->role)
                    ),
                
            ],
            [
                'label' => 'Fecha de Nacimiento',
                'field' => 'birth_date',
                'component' => 'admin.table-date',
                'format' => 'd/m/Y',
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
        :emptyMessage="$error ?? null"
        :error="isset($error) && !is_null($error)"
    />

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