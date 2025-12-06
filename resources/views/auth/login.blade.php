<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtentiDoc - Iniciar Sesión</title>
    <link rel="icon" href="Logo_Tran.png" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-teal-400 via-cyan-500 to-blue-600 flex items-center justify-center p-4">
    
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex flex-col md:flex-row min-h-[600px]">
            
            <!-- Left Panel - Logo & Branding -->
            <div class="w-full md:w-1/2 bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center p-8 md:p-12 relative">
                <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(circle_at_30%_50%,_rgba(22,163,74,0.3),transparent_50%),radial-gradient(circle_at_70%_50%,_rgba(37,99,235,0.3),transparent_50%)]"></div>
                
                <div class="relative z-10 text-center">
                    <!-- Logo -->
                    <div class="mb-6 inline-block">
                        <img src="./Logo_Tran.png" alt="Logo AtentiDoc">
                        {{-- <svg class="w-32 h-32 md:w-40 md:h-40" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Stethoscope -->
                            <path d="M100 140C100 140 85 155 70 155C55 155 45 145 45 130C45 115 55 105 70 105C85 105 100 120 100 120" 
                                  stroke="url(#gradient1)" stroke-width="8" stroke-linecap="round"/>
                            <path d="M100 140C100 140 115 155 130 155C145 155 155 145 155 130C155 115 145 105 130 105C115 105 100 120 100 120" 
                                  stroke="url(#gradient1)" stroke-width="8" stroke-linecap="round"/>
                            <circle cx="100" cy="85" r="12" fill="url(#gradient2)"/>
                            <line x1="100" y1="97" x2="100" y2="120" stroke="url(#gradient2)" stroke-width="6"/>
                            
                            <!-- Heart shape -->
                            <path d="M100 65 L95 55 C90 45 75 40 65 50 C55 60 60 75 70 85 L100 110 L130 85 C140 75 145 60 135 50 C125 40 110 45 105 55 Z" 
                                  fill="url(#gradient3)"/>
                            
                            <defs>
                                <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#1CD8D2;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2684FF;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="gradient2" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#1CD8D2;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2684FF;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="gradient3" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#EF4444;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#DC2626;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg> --}}
                    </div>
                    
                    <!-- Brand Name -->
                    <h1 class="text-4xl md:text-5xl font-bold mb-3">
                        <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">Atenti</span><span class="bg-gradient-to-r from-teal-500 to-green-500 bg-clip-text text-transparent">Doc</span>
                    </h1>
                    
                    <!-- Tagline -->
                    <p class="text-blue-600 text-lg font-medium">Siempre listos para cuidarte</p>
                </div>
            </div>
            
            <!-- Right Panel - Login Form -->
            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <div class="max-w-md mx-auto w-full">
                    
                    <h2 class="text-3xl font-bold text-gray-800 mb-8">Iniciar Sesión</h2>
                    
                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- Email Input -->
                        <x-input-field 
                            type="email"
                            name="email"
                            label="E-mail"
                            placeholder="Ingresá tu correo electrónico"
                            required
                        />
                        
                        <!-- Password Input -->
                        <x-input-field 
                            type="password"
                            name="password"
                            label="Contraseña"
                            placeholder="Ingresá tu contraseña"
                            :showPasswordToggle="true"
                            required
                            helperText="Debe contener al menos 1 mayúscula, 1 minúscula 1 número y 1 carácter especial"
                        />
                        
                        <!-- Remember & Forgot Password -->
                        <div class="flex items-center justify-between pt-2">
                            <x-checkbox 
                                name="remember"
                                label="Recordarme"
                            />
                            
                            <a href="#{{-- {{ route('password.request') }} --}}" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                Olvidaste tu contraseña
                            </a>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <x-primary-button>
                                Iniciar Sesión
                            </x-primary-button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
            
        </div>
    </div>

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
                        <b>
                            {{ session('error') }}
                        </b>
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

</body>
</html>

{{-- 
    ## 🗂️ Estructura de archivos
        resources/
        ├── views/
        │   ├── auth/
        │   │   └── login.blade.php
        │   └── components/
        │       ├── input-field.blade.php
        │       ├── primary-button.blade.php
        │       └── checkbox.blade.php
        ├── css/
        │   └── app.css
        └── js/
            └── app.js 
            
--}}