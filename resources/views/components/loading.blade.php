<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .spin {
        animation: spin 1.2s linear infinite;
    }
</style>

<template x-if="isLoading">
    <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"> 
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-all">

            <div style="
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            ">
                <img src="{{ asset('loading.png') }}" 
                    id="loading-image" 
                    class="spin"
                    alt="Imagen que indica que el sistema está trabajando en una consulta">
            </div>

        </div>
    </div>
</template>



