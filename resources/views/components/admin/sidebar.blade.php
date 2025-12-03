@props(['currentRoute' => ''])

<!-- Overlay for mobile -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
    style="display: none;"
></div>

<!-- Sidebar -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed lg:static inset-y-0 left-0 z-30 w-72 bg-gradient-to-b from-teal-500 to-emerald-600 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
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
                <button class="cursor-pointer">
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
                route="#{{-- admin.professionals --}}"
            />
            
            <x-admin.menu-item 
                icon="patients"
                label="Pacientes"
                :hasSubmenu="true"
                route="#{{-- admin.patients --}}"
            />
            
            <x-admin.menu-item 
                icon="users"
                label="Usuarios"
                :hasSubmenu="true"
                route="#{{-- admin.users --}}"
            />
        </div>
    </nav>
    
    <!-- Logo Section -->
    <div class="p-6 border-t border-white/20">
        <div class="flex flex-col items-center">
            <img src="./Logo_Blanco.png" alt="Logo de AtentiDoc" class="w-12 h-12 mb-2">
            {{-- <svg class="w-12 h-12 mb-2" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 140C100 140 85 155 70 155C55 155 45 145 45 130C45 115 55 105 70 105C85 105 100 120 100 120" 
                      stroke="white" stroke-width="8" stroke-linecap="round" opacity="0.9"/>
                <path d="M100 140C100 140 115 155 130 155C145 155 155 145 155 130C155 115 145 105 130 105C115 105 100 120 100 120" 
                      stroke="white" stroke-width="8" stroke-linecap="round" opacity="0.9"/>
                <circle cx="100" cy="85" r="12" fill="white" opacity="0.9"/>
                <line x1="100" y1="97" x2="100" y2="120" stroke="white" stroke-width="6" opacity="0.9"/>
                <path d="M100 65 L95 55 C90 45 75 40 65 50 C55 60 60 75 70 85 L100 110 L130 85 C140 75 145 60 135 50 C125 40 110 45 105 55 Z" 
                      fill="#EF4444"/>
            </svg> --}}
            <h3 class="text-lg font-bold text-white">AtentiDoc</h3>
            <p class="text-xs text-teal-100">Siempre listos para cuidarte</p>
        </div>
    </div>
</aside>