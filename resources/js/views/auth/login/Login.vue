<template>
    <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 text-white bg-cover bg-center bg-no-repeat relative" style="background-image: url('/images/home/imagen_registrologin.png');">
        <div class="absolute inset-0 bg-[#0b0f19]/70 backdrop-blur-[2px]"></div>
        <div class="max-w-md w-full relative z-10">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold font-orbitron tracking-tighter">
                    Bienvenido a Trade<span class="text-[#5369F2]">Game</span>Sense
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Inicia sesión para continuar
                </p>
            </div>

            <!-- Formulario -->
            <Card class="!bg-[#111827] !border-gray-800 !text-white shadow-2xl">
                <template #content>
                    <form @submit.prevent="submitLogin" class="space-y-6">
                        <!-- Email -->
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-medium">Correo electrónico</label>
                            <InputText
                                id="email"
                                type="email"
                                v-model="loginForm.email"
                                placeholder="tu@email.com"
                                class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                :class="{ 'p-invalid': validationErrors?.email }"
                            />
                            <small v-if="validationErrors?.email" class="text-red-500">
                                <div v-for="message in validationErrors.email" :key="message">
                                    {{ message }}
                                </div>
                            </small>
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col gap-2">
                            <label for="password" class="font-medium">Contraseña</label>
                            <Password
                                id="password"
                                v-model="loginForm.password"
                                placeholder="••••••••"
                                :toggleMask="true"
                                :feedback="false"
                                inputClass="w-full !bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                :class="{ 'p-invalid': validationErrors?.password }"
                                fluid
                            />
                            <small v-if="validationErrors?.password" class="text-red-500">
                                <div v-for="message in validationErrors.password" :key="message">
                                    {{ message }}
                                </div>
                            </small>
                        </div>

                        <!-- Remember me y Forgot password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    v-model="loginForm.remember"
                                    inputId="remember"
                                    binary
                                />
                                <label for="remember" class="text-sm cursor-pointer">
                                    Recuérdame
                                </label>
                            </div>
                            <router-link
                                :to="{ name: 'auth.forgot-password' }"
                                class="text-sm font-medium text-blue-500 hover:text-blue-400 transition-colors"
                            >
                                ¿Olvidaste tu contraseña?
                            </router-link>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            label="Iniciar Sesión"
                            :loading="processing"
                            :disabled="processing"
                            class="w-full !bg-[#5369F2] !border-[#5369F2] hover:!bg-blue-600"
                            size="large"
                        />

                        <!-- Register link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-400">
                                ¿No tienes una cuenta?
                                <router-link
                                    :to="{ name: 'auth.register' }"
                                    class="font-medium text-blue-500 hover:text-blue-400 transition-colors"
                                >
                                    Regístrate aquí
                                </router-link>
                            </p>
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import useAuth from '@/composables/auth';

const { loginForm, validationErrors, processing, submitLogin } = useAuth();
</script>

<style scoped>
:deep(.p-checkbox .p-checkbox-box.p-highlight),
:deep(.p-checkbox.p-checkbox-checked .p-checkbox-box) {
    background: #5369F2 !important;
    border-color: #5369F2 !important;
}
</style>

<style scoped>
/* Asegurar que PrimeIcons se muestren correctamente */
:deep(.pi) {
    font-family: 'primeicons' !important;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    display: inline-block;
}

/* Estilos para InputText de PrimeVue */
:deep(.p-inputtext) {
    width: 100%;
}

/* Estilos para Password de PrimeVue */
:deep(.p-password) {
    width: 100%;
}

:deep(.p-password-input) {
    width: 100%;
}

/* Estilos para Button de PrimeVue */
:deep(.p-button) {
    width: 100%;
}

@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap');
.font-orbitron {
    font-family: 'Orbitron', sans-serif !important;
}
</style>
