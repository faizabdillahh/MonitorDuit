<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-[#fafafa] dark:bg-black px-4"
    >
        <div class="w-full max-w-sm">
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                        <span class="text-white font-bold text-lg">M</span>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-gray-900 dark:text-white" style="font-family: 'Plus Jakarta Sans', sans-serif;">MonitorDuit</span>
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
