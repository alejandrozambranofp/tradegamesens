<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#0b0f19] text-white">
        <div class="max-w-2xl w-full">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold font-orbitron tracking-tighter">
                    Únete a <span class="text-[#5369F2]">nosotros</span>!
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Regístrate para comenzar
                </p>
            </div>

            <!-- Formulario -->
            <Card class="!bg-[#111827] !border-gray-800 !text-white shadow-2xl">
                <template #content>
                    <form @submit.prevent="submitRegister" class="space-y-6">
                        <!-- Name -->
                        <div class="flex flex-col gap-2">
                            <label for="name" class="font-medium">Nombre</label>
                            <InputText
                                id="name"
                                v-model="registerForm.name"
                                placeholder="Nombre completo"
                                class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                :invalid="!!validationErrors?.name"
                            />
                            <small v-if="validationErrors?.name" class="text-red-500">
                                {{ validationErrors.name[0] }}
                            </small>
                        </div>

                        <!-- Surname1 y Surname2 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="surname1" class="font-medium">Primer apellido</label>
                                <InputText
                                    id="surname1"
                                    v-model="registerForm.surname1"
                                    placeholder="Primer apellido"
                                    class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                    :invalid="!!validationErrors?.surname1"
                                />
                                <small v-if="validationErrors?.surname1" class="text-red-500">
                                    {{ validationErrors.surname1[0] }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="surname2" class="font-medium">Segundo apellido</label>
                                <InputText
                                    id="surname2"
                                    v-model="registerForm.surname2"
                                    placeholder="Segundo apellido"
                                    class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                    :invalid="!!validationErrors?.surname2"
                                />
                                <small v-if="validationErrors?.surname2" class="text-red-500">
                                    {{ validationErrors.surname2[0] }}
                                </small>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-medium">Correo electrónico</label>
                            <InputText
                                id="email"
                                type="email"
                                v-model="registerForm.email"
                                placeholder="tu@email.com"
                                class="!bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                :invalid="!!validationErrors?.email"
                            />
                            <small v-if="validationErrors?.email" class="text-red-500">
                                {{ validationErrors.email[0] }}
                            </small>
                        </div>

                        <!-- Password y Confirm Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="password" class="font-medium">Contraseña</label>
                                <Password
                                    id="password"
                                    v-model="registerForm.password"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    inputClass="w-full !bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                    :invalid="!!validationErrors?.password"
                                    fluid
                                />
                                <small v-if="validationErrors?.password" class="text-red-500">
                                    {{ validationErrors.password[0] }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="font-medium">Confirmar contraseña</label>
                                <Password
                                    id="password_confirmation"
                                    v-model="registerForm.password_confirmation"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    inputClass="w-full !bg-[#0b0f19] !border-white/10 !text-white !p-3 !rounded-xl focus:!border-[#5369F2] !shadow-none"
                                    :invalid="!!validationErrors?.password_confirmation"
                                    fluid
                                />
                                <small v-if="validationErrors?.password_confirmation" class="text-red-500">
                                    {{ validationErrors.password_confirmation[0] }}
                                </small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            label="Registrarse"
                            :loading="processing"
                            :disabled="processing"
                            class="w-full !bg-[#5369F2] !border-[#5369F2] hover:!bg-blue-600"
                            size="large"
                        />

                        <!-- Login link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-400">
                                ¿Ya tienes una cuenta?
                                <router-link
                                    :to="{ name: 'auth.login' }"
                                    class="font-medium text-blue-500 hover:text-blue-400 transition-colors"
                                >
                                    Inicia sesión aquí
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

const { registerForm, validationErrors, processing, submitRegister } = useAuth();
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap');
.font-orbitron {
    font-family: 'Orbitron', sans-serif !important;
}
</style>
