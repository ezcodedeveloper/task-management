<script setup lang="ts">
import { ref, computed } from 'vue'
import TextLink from '@/components/TextLink.vue';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const focusedField = ref(null)

const errors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const inputClass = (field: keyof typeof form.value) => {
    return [
        'h-12 w-full rounded-md border bg-gray-50 px-3 text-base transition-all focus:outline-none',
        errors.value[field] ? 'border-red-500 bg-red-50' : '',
        !errors.value[field] && focusedField.value === field
            ? 'border-black ring-2 ring-black/20 bg-white'
            : 'border-gray-200',
    ].join(' ')
}

const passwordStrength = computed(() => {
    const value = form.value.password
    let score = 0

    if (!value) return { score, label: '', color: '', textClass: '' }
    if (value.length >= 8) score++
    if (/[A-Z]/.test(value)) score++
    if (/[a-z]/.test(value)) score++
    if (/\d/.test(value)) score++
    if (/[^A-Za-z0-9]/.test(value)) score++
    if (value.length >= 12) score++

    let label = 'Weak'
    let color = 'bg-red-500'
    let textClass = 'text-red-500'

    if (score >= 4) {
        label = 'Medium'
        color = 'bg-yellow-500'
        textClass = 'text-yellow-500'
    }
    if (score >= 5) {
        label = 'Strong'
        color = 'bg-green-500'
        textClass = 'text-green-500'
    }

    return { score, label, color, textClass }
})



const validateEmail = (email: string) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
}

const handleBlur = (field: keyof typeof form.value) => {
    focusedField.value = null

    if (field === 'email') {
        if (form.value.email && !validateEmail(form.value.email)) {
            errors.value.email = 'Enter a valid email address'
        } else {
            errors.value.email = ''
        }
    }
}

const handleSignUp = async () => {
    errors.value = {
        name: !form.value.name ? 'Name is required' : '',
        email: !form.value.email
            ? 'Email is required'
            : !validateEmail(form.value.email)
                ? 'Enter a valid email address'
                : '',
        password: !form.value.password ? 'Password is required' : '',
        password_confirmation:
            form.value.password_confirmation !== form.value.password
                ? 'Passwords do not match'
                : '',
    };
    if (passwordStrength.value.label === 'Weak' || passwordStrength.value.label === 'Medium') {
        errors.value.password = 'Password is too weak. Please choose a stronger password.';
        return;  // stop form submission
    }
    if (Object.values(errors.value).some((err) => err)) return

    // Handle sign up logic here
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form.value),
        });

        if (!response.ok) {
            const data = await response.json();

            // Email error
            if (response.status === 422 && data.errors) {
                if (data.errors.email) {
                    errors.value.email = data.errors.email[0]; // "Email already exists" or other msg
                }
                if (data.errors.password) {
                    errors.value.password = data.errors.password[0]; // "The password field must be at least 8 characters."
                }
            }

            return; // Stop here after setting errors
        }
        window.location.href = '/dashboard';
        // Successful signup
        console.log('Signed up successfully');
    } catch (error) {
        console.error('Error signing up:', error);
        // Handle network or other errors
    }
}
</script>
<template>
    <div class="flex min-h-screen w-full flex-col items-center justify-center bg-white">
        <div class="absolute top-8 flex items-center gap-1.5 text-lg font-semibold">
            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-pink-500 text-white">
                <ChartBarIcon class="h-4 w-4" />
            </div>
            <span class="text-pink-500">Tawasul</span>
        </div>

        <div class="w-full max-w-md px-4 flex flex-col items-center mt-16">
            <div class="mb-6 text-center">
                <h1 class="mb-2 text-2xl font-semibold">Sign Up</h1>
                <p class="text-sm text-gray-600">Create your account to get started</p>
            </div>

            <form @submit.prevent="handleSignUp" class="w-full space-y-4">
                <!-- Name Field -->
                <div class="space-y-1">
                    <label for="name" class="text-sm font-medium">Full Name</label>
                    <input id="name" type="text" v-model="form.name" :class="inputClass('name')"
                        placeholder="Enter your full name" />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
                </div>

                <!-- Email Field -->
                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium">Email</label>
                    <input id="email" type="text" v-model="form.email" @blur="handleBlur('email')"
                        :class="inputClass('email')" placeholder="Enter your email" />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                </div>

                <!-- Password Field -->
                <div class="space-y-1">
                    <label for="password" class="text-sm font-medium">Password</label>
                    <input id="password" type="password" v-model="form.password" :class="inputClass('password')"
                        placeholder="Create a password" />
                    <!-- Password Strength -->
                    <div v-if="form.password" class="mt-2">
                        <div class="flex items-center justify-between mb-1">
                            <div class="h-2 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-300" :class="passwordStrength.color"
                                    :style="{ width: (passwordStrength.score / 6) * 100 + '%' }"></div>
                            </div>
                            <span :class="'ml-2 text-xs font-medium ' + passwordStrength.textClass">
                                {{ passwordStrength.label }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">Use 8+ characters with a mix of letters, numbers & symbols
                        </div>
                    </div>
                    <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</p>
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="text-sm font-medium">Confirm Password</label>
                    <input id="password_confirmation" type="password" v-model="form.password_confirmation"
                        :class="inputClass('password_confirmation')" placeholder="Confirm your password" />
                    <p v-if="errors.password_confirmation" class="mt-1 text-xs text-red-500">{{
                        errors.password_confirmation }}</p>
                </div>

                <button type="submit"
                    class="w-full bg-black text-white hover:bg-black/90 cursor-pointer h-12 rounded-md">
                    Sign Up
                </button>
            </form>
            <p class="mt-6 text-sm text-center">
                Already have an account?
                <TextLink :href="route('login')" class="text-pink-500 hover:underline">Sign In</TextLink>
            </p>
        </div>
    </div>
</template>
