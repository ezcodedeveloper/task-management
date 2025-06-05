<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="flex min-h-screen w-full flex-col items-center justify-center bg-white">
        <div class="absolute top-8 flex items-center gap-1.5 text-lg font-semibold">
            <span class="text-pink-500">Tawasul</span>
        </div>

        <div class="w-full max-w-md px-4 flex flex-col items-center mt-16">
            <div class="mb-6 text-center">
                <h1 class="mb-2 text-2xl font-semibold">Sign In</h1>
                <p class="text-sm text-gray-600">Enter your credentials to access your account</p>
            </div>

            <form @submit.prevent="submit" class="w-full space-y-4">
                <!-- Email Input -->
                <div class="space-y-1">
                    <label for="email" class="text-sm font-medium">Email</label>
                    <input id="email" type="email" v-model="form.email"
                        class="h-12 w-full rounded-md border bg-gray-50 px-3 text-base transition-all" :class="{
                            'border-red-500 bg-red-50': form.errors.email,
                            'border-black ring-2 ring-black/20 bg-white': !form.errors.email,
                            'border-gray-200': !form.errors.email
                        }" placeholder="Enter your email" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>

                </div>

                <!-- Password Input -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-medium">Password</label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-pink-500 hover:underline">
                            Forgot password?
                        </TextLink>
                    </div>
                    <input id="password" type="password" v-model="form.password"
                        class="h-12 w-full rounded-md border bg-gray-50 px-3 text-base transition-all" :class="{
                            'border-red-500 bg-red-50': form.errors.email,
                            'border-black ring-2 ring-black/20 bg-white': !form.errors.password,
                            'border-gray-200': !form.errors.password
                        }" placeholder="Enter your password" />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-black text-white hover:bg-black/90 cursor-pointer py-2 rounded-md">
                    Sign In
                </button>
            </form>

            <div class="mt-6 flex flex-col items-center space-y-2 text-sm text-center">
                <p>
                    Don’t have an account?
                    <TextLink :href="route('register')" class="text-pink-500 hover:underline">Sign Up</TextLink>
                </p>
            </div>
        </div>
    </div>
</template>
