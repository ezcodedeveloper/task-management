<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
interface Quote {
    content: string
    author: string
}

const quote = ref<Quote | null>(null)
const error = ref<string | null>(null)
const loading = ref(false)

onMounted(async () => {
    loading.value = true
    error.value = null
    try {
        const response = await fetch('/api/get-quote')
        const data = await response.json()

        if (data.success) {
            quote.value = data.quote
        } else {
            error.value = data.message || 'Failed to load quote.'
        }
    } catch (err) {
        error.value = 'An unexpected error occurred.'
    } finally {
        loading.value = false
    }
})


</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 ">

                <div
                    class=" w-full space-y-4 rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-200/10 dark:bg-blue-700/10">
                    <div class="relative space-y-0.5 text-blue-600 dark:text-blue-100">
                        <p class="font-medium">Quote of the day!</p>
                        <p class="text-sm">
                        <div v-if="error" class="error">{{ error }}</div>
                        <div v-if="loading">Loading...</div>
                        <div v-if="quote">
                            <p>"{{ quote.content }}"</p>
                            <small>— {{ quote.author }}</small>
                        </div>
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
