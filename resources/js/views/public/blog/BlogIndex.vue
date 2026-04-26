<template>
    <div class="min-h-screen bg-[#0F172A] pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <header class="text-center mb-16 space-y-4">
                <h1 class="text-4xl md:text-6xl font-black text-white font-orbitron tracking-tighter uppercase">
                    Trade<span class="text-[#5369F2]">Blog</span>
                </h1>
                <p class="text-gray-400 font-inter text-lg max-w-2xl mx-auto">
                    Novedades, actualizaciones y lo mejor de la comunidad TradeGameSense.
                </p>
                <div class="w-24 h-1 bg-[#5369F2] mx-auto rounded-full"></div>
            </header>

            <!-- Blog Post -->
            <article class="bg-[#1E293B] rounded-[40px] border border-white/5 overflow-hidden shadow-2xl transition-all hover:border-[#5369F2]/30 group">
                <!-- Hero Image Placeholder -->
                <div class="h-64 md:h-96 bg-[#0b0f19] relative overflow-hidden flex items-center justify-center">
                    <img src="/images/home/home-hero.png" alt="Bienvenida" class="w-full h-full object-cover opacity-50 group-hover:scale-105 transition-transform duration-1000" />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1E293B] to-transparent"></div>
                    <div class="absolute bottom-8 left-8">
                        <Tag value="Anuncio Oficial" severity="info" class="!bg-[#5369F2] !text-white !font-bold" />
                    </div>
                </div>

                <div class="p-8 md:p-12 space-y-6">
                    <div class="flex items-center gap-4 text-xs text-gray-400 font-bold uppercase tracking-widest">
                        <span>26 Abril, 2026</span>
                        <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                        <span>Admin</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-white font-orbitron leading-tight">
                        ¡Bienvenidos a TradeGameSense! La nueva era de las guías gaming ha comenzado
                    </h2>

                    <div class="prose prose-invert max-w-none text-gray-300 font-inter leading-relaxed space-y-4">
                        <p class="text-lg">
                            Estamos emocionados de abrir finalmente las puertas de <strong>TradeGameSense</strong>, la plataforma definitiva diseñada por y para jugadores que buscan dominar sus títulos favoritos.
                        </p>
                        
                        <p>
                            TradeGameSense nace de una necesidad clara: encontrar información de calidad, validada y bien organizada en un ecosistema gaming cada vez más fragmentado. Aquí, no solo encontrarás guías; encontrarás estrategias maestras compartidas por la propia comunidad.
                        </p>

                        <h3 class="text-white font-bold text-xl mt-8">¿Qué encontrarás en este blog?</h3>
                        <p>
                            Este espacio será el corazón de nuestra comunicación contigo. Aquí publicaremos:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li><strong>Actualizaciones de la Plataforma:</strong> Nuevas funcionalidades, mejoras en la interfaz y herramientas para creadores.</li>
                            <li><strong>Información de la Web:</strong> Avisos importantes, mantenimientos y eventos de la comunidad.</li>
                            <li><strong>Avisos de Nuevas Guías:</strong> Destacaremos aquellas guías excepcionales o de juegos recién lanzados que están marcando tendencia.</li>
                        </ul>

                        <p class="pt-6 italic text-gray-400">
                            "Nuestra misión es elevar el nivel de juego de cada miembro de nuestra comunidad, proporcionando las herramientas necesarias para que cada partida sea una victoria."
                        </p>
                        
                        <p>
                            Gracias por formar parte de este lanzamiento. ¡Es hora de empezar a jugar con sentido!
                        </p>
                    </div>

                    <!-- Footer Post -->
                    <div class="pt-8 border-t border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <Avatar image="/images/logo.png" shape="circle" class="border border-white/10" />
                            <div>
                                <p class="text-white font-bold text-sm">Equipo TradeGameSense</p>
                                <p class="text-gray-500 text-xs">Administración</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button icon="pi pi-share-alt" text rounded class="!text-gray-400 hover:!text-white" />
                            <Button icon="pi pi-heart" text rounded class="!text-gray-400 hover:!text-red-500" />
                        </div>
                    </div>
                </div>
            </article>

            <!-- Newsletter Placeholder -->
            <div class="mt-20 p-10 bg-gradient-to-r from-[#5369F2]/20 to-transparent rounded-[40px] border border-[#5369F2]/20 text-center space-y-6">
                <h3 class="text-2xl font-bold text-white font-orbitron">No te pierdas nada</h3>
                <p class="text-gray-400">Suscríbete para recibir las mejores guías y noticias directamente en tu correo.</p>
                <div class="flex max-w-md mx-auto gap-2">
                    <InputText placeholder="Tu email..." class="!bg-[#0F172A] !border-white/10 !rounded-xl flex-grow" />
                    <Button label="Suscribirse" class="!bg-[#5369F2] !border-none !rounded-xl !px-8" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
</script>

<style scoped>
.font-orbitron { font-family: 'Orbitron', sans-serif; }
.font-inter { font-family: 'Inter', sans-serif; }
</style>
