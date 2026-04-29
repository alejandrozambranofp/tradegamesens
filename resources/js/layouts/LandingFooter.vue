<template>
    <footer class="bg-[#1E293B] pt-16 pb-8 relative overflow-hidden font-montserrat">
        <!-- Glow effects para mantener la estética gaming -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#5369F2]/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-16">
                
                <!-- Columna 1: Branding -->
                <div class="md:col-span-1">
                    <router-link to="/" class="flex items-center gap-3 mb-6 group inline-flex">
                        <img src="/images/logo.png" alt="Logo" class="h-8 w-auto transition-transform group-hover:scale-110"/>
                        <span class="text-white text-lg font-bold font-orbitron tracking-wider">
                            Trade<span class="text-[#5369F2]">Game</span>Sense
                        </span>
                    </router-link>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Accede a gameplays, datos y enlaces de tus juegos favoritos. Únete a nuestra comunidad gamer.
                    </p>
                </div>

                <!-- Columna 2: Soporte -->
                <div class="md:col-span-1">
                    <h3 class="text-white text-lg font-bold mb-6">Soporte</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Preguntas frecuentes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Contacto</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Reportar error</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Estado del servidor</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Legal -->
                <div class="md:col-span-1">
                    <h3 class="text-white text-lg font-bold mb-6">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Términos y condiciones</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">DMCA</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Política de privacidad</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Más -->
                <div class="md:col-span-1">
                    <h3 class="text-white text-lg font-bold mb-6">Más</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Historial</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Donaciones</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Sobre nosotros</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#5369F2] text-sm transition-colors">Roadmap</a></li>
                    </ul>
                </div>

            </div>

            <!-- Footer Bottom: Iconos y Copyright alineados a la derecha -->
            <div class="mt-20 flex flex-col md:flex-row items-center justify-end gap-6">
                
                <!-- Social Icons -->
                <div class="flex items-center gap-4">
                    <a href="#" class="text-white hover:text-[#5369F2] transition-colors">
                        <i class="pi pi-whatsapp text-xl"></i>
                    </a>
                    <a href="#" class="text-white hover:text-[#5369F2] transition-colors">
                        <i class="pi pi-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-white hover:text-[#5369F2] transition-colors">
                        <i class="pi pi-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-white hover:text-[#5369F2] transition-colors">
                        <i class="pi pi-discord text-xl"></i>
                    </a>
                </div>

                <!-- Copyright -->
                <p class="text-white text-xs font-montserrat">
                    Copyright © {{ new Date().getFullYear() }} | TradeGameSense
                </p>

            </div>
        </div>
    </footer>
</template>

<script setup>
</script>
