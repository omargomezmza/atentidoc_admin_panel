@props(['currentRoute' => ''])

<!-- Overlay for mobile con efecto blur -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-20 lg:hidden"
    style="display: none;"
></div>

<!-- Sidebar -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:-translate-x-64'"
    class="fixed lg:static inset-y-0 left-0 z-30 w-72 bg-gradient-to-b from-teal-500 to-emerald-600 text-white transform transition-transform duration-300 ease-in-out flex flex-col"
    x-cloak
>
    <!-- User Profile Section -->
    <div class="p-6 border-b border-white/20">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 rounded-full bg-white/20 border-4 border-white/30 overflow-hidden mb-3 ring-4 ring-white/10">
                <img 
                    src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Usuario') . '&background=0D9488&color=fff' }}" 
                    alt="Avatar"
                    class="w-full h-full object-cover"
                >
            </div>
            <h2 class="text-xl font-bold text-white">{{ auth()->user()->email ? explode('@', auth()->user()->email)[0] : 'un_usuario' }}</h2>
            <p class="text-teal-100 text-sm font-medium">Administrador</p>

            <form class="mt-5" action="{{ route('logout') }}" method="post">
                @csrf
                <button class="cursor-pointer hover:text-white/80 transition-colors">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    
    <!-- Navigation Section -->
    <nav class="flex-1 px-4 py-6 overflow-y-auto">
        <p class="px-3 mb-3 text-xs font-semibold text-teal-100 uppercase tracking-wider">Opciones</p>
        
        <div class="space-y-1">
            <x-admin.menu-item 
                icon="professionals"
                label="Profesionales"
                :hasSubmenu="true"
                route="#"
            />
            
            <x-admin.menu-item 
                icon="patients"
                label="Pacientes"
                :hasSubmenu="true"
                route="#"
            />
            
            <x-admin.menu-item 
                icon=""
                label="Recursos"
                :hasSubmenu="true"
                route="#"
            >
                <x-admin.menu-item 
                    icon="users"
                    label="Doctores"
                    :hasSubmenu="false"
                    route="admin.list.doctor"
                />

                <x-admin.menu-item 
                    icon="users"
                    label="Pacientes"
                    :hasSubmenu="false"
                    route="admin.list.patient"
                />

                <x-admin.menu-item 
                    icon="users"
                    label="Admins"
                    :hasSubmenu="false"
                    route="admin.list.admin"
                />
            </x-admin.menu-item>
        </div>
    </nav>
    
    <!-- Logo Section -->
    <div class="p-6 border-t border-white/20">
        <div class="flex flex-col items-center">
            <img src="{{ asset('Logo_Blanco.png') }}" alt="Logo de AtentiDoc" class="w-12 h-12 mb-2">
            <h3 class="text-lg font-bold text-white">AtentiDoc</h3>
            <p class="text-xs text-teal-100">Siempre listos para cuidarte</p>
        </div>
    </div>
</aside>

<!-- Toggle Button (visible cuando sidebar está cerrado en desktop) -->
<button
    @click="sidebarOpen = !sidebarOpen"
    {{-- :class="sidebarOpen ? 'lg:hidden' : ''" --}}
    class="fixed bottom-8 left-4 lg:left-6 z-40 p-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-full shadow-2xl hover:shadow-teal-500/50 transition-all duration-300 hover:scale-110 group"
    :title="sidebarOpen ? 'Cerrar menú' : 'Abrir menú'"
>
    <!-- Icono de hamburguesa cuando está cerrado -->
    <svg 
        x-show="!sidebarOpen" 
        class="w-6 h-6 transition-transform group-hover:rotate-90 duration-300" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
        style="display: none;"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
    
    <!-- Icono de cerrar cuando está abierto (solo mobile) -->
    <svg 
        x-show="sidebarOpen" 
        class="w-6 h-6 {{-- lg:hidden --}} transition-transform group-hover:rotate-90 duration-300" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>