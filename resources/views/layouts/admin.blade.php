<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administrador') - AtentiDoc</title>
    <link rel="icon" href="{{ asset('Logo_Tran.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    
    {{-- Alpine data global para sidebar y loading --}}
    <div x-data="{ 
        sidebarOpen: window.innerWidth >= 1024, 
        isLoading: false 
    }" class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <x-admin.sidebar />
        
        <!-- Main Content Area -->
        <div 
            class="flex-1 flex flex-col overflow-hidden transition-all duration-300"
            :class="sidebarOpen ? 'lg:ml-0' : 'lg:ml-0'"
        >
            
            <!-- Top Header (solo para mobile) -->
            {{-- <header class="bg-white border-b border-gray-200 lg:hidden">
                <div class="px-4 py-3 flex items-center justify-between">
                    <h1 class="text-xl font-bold text-gray-800">AtentiDoc</h1>
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </header> --}}
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
            
        </div>
        
        {{-- Modales de éxito/error --}}
        @if(session('success'))
            <x-modal show="true" name="init-success-modal" title="Proceso exitoso" size="md">
                <div class="space-y-4">
                    <h2>¡El proceso fue exitoso!</h2>
                </div>

                <x-slot:footer>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="$dispatch('close-modal', { name: 'init-success-modal' })" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            OK
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal>
        @endif  

        @if(session('error'))
            <x-modal show="true" name="init-error-modal" title="Ocurrió un error" size="md">
                <div class="space-y-4">
                    <h2>Revisa los errores y vuelve a intentarlo.</h2>

                    @if (session('exception'))
                        <p><i>{{ session('exception') }}</i></p>
                    @else
                        <p>{{ session('error') }}</p>

                        @php
                            $details = session('details');
                        @endphp
                        @if (isset($details) && !empty($details)) 
                            @foreach (session('details') as $detail)
                                <p>
                                    <b>{{ $detail['field'] }}:</b>
                                    <i>{{ $detail['message'] }}</i>
                                </p>
                            @endforeach    
                        @endif
                    @endif
                </div>

                <x-slot:footer>
                    <div class="flex justify-end space-x-3">                        
                        <button type="button" @click="$dispatch('close-modal', { name: 'init-error-modal' })"  
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            OK
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal>
        @endif  

        <x-modal name="success-modal" title="Proceso exitoso" size="md">
            <div class="space-y-4">
                <h2>¡El proceso fue exitoso!</h2>
            </div>

            <x-slot:footer>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="$dispatch('close-modal', { name: 'success-modal' })" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        OK
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>

        <x-modal name="error-modal" title="Ocurrió un error" size="md">
            <div class="space-y-4">
                <h2>Por favor, intentalo de nuevo más tarde.</h2>
            </div>

            <x-slot:footer>
                <div class="flex justify-end space-x-3">                        
                    <button type="button" @click="$dispatch('close-modal', { name: 'error-modal' })"  
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        OK
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>

        <x-loading />
    </div>

    @stack('scripts')
</body>
</html>