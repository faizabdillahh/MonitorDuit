<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-[#fafafa] dark:bg-black px-4"
    >
        <div class="w-full max-w-sm">
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center">
                    <ApplicationLogo class="h-10 w-auto" />
                </Link>
            </div>

            <div
                class="w-full bg-white dark:bg-gray-900 px-6 py-8 border border-gray-200 dark:border-gray-800 rounded-xl"
            >
                <slot />
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-[13px] text-gray-400">
                    &copy; {{ new Date().getFullYear() }} MonitorDuit.
                </p>
            </div>
        </div>
    </div>
</template>
