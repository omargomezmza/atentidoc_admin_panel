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
    
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Sidebar -->
        <x-admin.sidebar />
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Header (optional, for mobile menu toggle) -->
            <header class="bg-white border-b border-gray-200 lg:hidden">
                <div class="px-4 py-3 flex items-center justify-between">
                    <h1 class="text-xl font-bold text-gray-800">AtentiDoc</h1>
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
            
        </div>
        
    </div>
    

        @if(session('success'))
            <x-modal show="true" name="success-modal" title="Proceso exitoso" size="md">
                <div class="space-y-4">
                    <h2> 
                        ¡El proceso fue exitoso!
                    </h2>

                </div>

                <x-slot:footer>
                    <div class="flex justify-end space-x-3">
                        
                        <button type="button" @click="$dispatch('close-modal', { name: 'success-modal' })" 
                             class="px-4 py-2 bg-green-600 text-white rounded-lg 
                            hover:bg-green-700 transition-colors">
                            OK
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal>
        @endif  


        @if(session('error'))
            <x-modal show="true" name="error-modal" title="Ocurrió un error" size="md">
                <div class="space-y-4">
                    <h2> 
                        Revisa los errores y vuelve a intentarlo.
                    </h2>

                    @if (session('exception'))

                        <p>
                            <i>
                                {{ session('exception') }}
                            </i>
                        </p>
                    @else

                        <p>
                            {{ session('error') }}
                        </p>
                        @foreach (session('details') as $detail)
                            <p>
                                <b>
                                    {{ $detail['field'] }}:
                                </b>
                                <i>
                                    {{ $detail['message'] }}
                                </i>
                            </p>
                        @endforeach
                        
                    @endif

                </div>

                <x-slot:footer>
                    <div class="flex justify-end space-x-3">                        
                        <button type="button" @click="$dispatch('close-modal', { name: 'error-modal' })"  
                            type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg 
                            hover:bg-red-700 transition-colors">
                            OK
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal>
        @endif  
        

    @stack('scripts')
</body>
</html>