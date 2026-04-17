import { ref, reactive, inject } from 'vue'
import { useRouter } from "vue-router";
import { AbilityBuilder, createMongoAbility } from '@casl/ability';
import { ABILITY_TOKEN } from '@casl/vue';
import { authStore } from "../store/auth";

export default function useAuth() {
    const processing = ref(false)
    const validationErrors = ref({})
    const router = useRouter()
    const swal = inject('$swal')
    const ability = inject(ABILITY_TOKEN)
    const auth = authStore()

    // En lugar de una variable externa, usamos una referencia reactiva al store
    // Esto evita que los datos se pierdan al navegar
    const user = auth.user 

    const loginForm = reactive({
        email: '',
        password: '',
        remember: false
    })

    const forgotForm = reactive({
        email: '',
    })

    const resetForm = reactive({
        email: '',
        token: '',
        password: '',
        password_confirmation: ''
    })

    const registerForm = reactive({
        name: '',
        surname1: '',
        surname2: '',
        email: '',
        password: '',
        password_confirmation: ''
    })

    const loginUser = async () => {
        console.log('GettingUserSignIn: loginUser')
        // Actualizamos las habilidades de CASL basadas en los permisos del usuario
        await getAbilities()
    }

    const submitLogin = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/login', loginForm)
            .then(async response => {
                // 1. Obtenemos el usuario del servidor
                await auth.getUser() 
                
                // 2. IMPORTANTE: Guardamos en localStorage para que el F5 no borre nada
                localStorage.setItem('user', JSON.stringify(auth.user))
                
                // 3. Cargamos permisos
                await loginUser()

                swal({
                    icon: 'success',
                    title: 'Login correcto',
                    showConfirmButton: false,
                    timer: 1500
                })

                // Redirección basada en roles
                const isAdmin = auth.user?.roles?.some(r => r.name.toLowerCase().includes('admin'));
                
                if (isAdmin) {
                    await router.push({ name: 'admin.index' })
                } else {
                    await router.push({ name: 'home' })
                }
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const submitRegister = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/register', registerForm)
            .then(async response => {
                swal({
                    icon: 'success',
                    title: 'Registration successfully',
                    showConfirmButton: false,
                    timer: 1500
                })
                await router.push({ name: 'auth.login' })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const submitForgotPassword = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/api/forget-password', forgotForm)
            .then(async response => {
                swal({
                    icon: 'success',
                    title: 'We have emailed your password reset link!',
                    showConfirmButton: false,
                    timer: 1500
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const submitResetPassword = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/api/reset-password', resetForm)
            .then(async response => {
                swal({
                    icon: 'success',
                    title: 'Password successfully changed.',
                    showConfirmButton: false,
                    timer: 1500
                })
                await router.push({ name: 'auth.login' })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const getUser = async () => {
        if (auth.authenticated) {
            await auth.getUser()
            await loginUser()
        }
    }

    const getUserSignIn = async () => {
        if (auth.authenticated) {
            await auth.getUserSignIn()
            await loginUser()
        }
    }

    const logout = async () => {
        if (processing.value) return

        processing.value = true

        axios.post('/logout')
            .then(response => {
                // Limpiamos store y localstorage
                auth.logout()
                localStorage.removeItem('user')
                router.push({ name: 'auth.login' })
            })
            .catch(error => {
                console.error(error)
            })
            .finally(() => {
                processing.value = false
            })
    }

    const getAbilities = async () => {
        await axios.get('/api/abilities')
            .then(response => {
                const permissions = response.data
                const { can, rules } = new AbilityBuilder(createMongoAbility)
                can(permissions)
                ability.update(rules)
            })
            .catch(e => console.error("Error cargando habilidades:", e))
    }

    return {
        loginForm,
        registerForm,
        forgotForm,
        resetForm,
        validationErrors,
        processing,
        submitLogin,
        submitRegister,
        submitForgotPassword,
        submitResetPassword,
        user,
        getUser,
        getUserSignIn,
        logout,
        getAbilities
    }
}