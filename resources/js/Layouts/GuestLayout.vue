<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-slate-50 pt-6 sm:pt-0 dark:bg-slate-950 px-4 relative overflow-hidden"
    >
        <!-- Background Glows -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-emerald-500/10 dark:bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 group-hover:scale-105 transition-transform duration-300">
                        <span class="text-white font-bold text-xl">M</span>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900 dark:text-white">MonitorDuit</span>
                </Link>
            </div>

            <div
                class="mt-6 w-full overflow-hidden bg-white/80 px-8 py-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] backdrop-blur-xl sm:rounded-3xl border border-slate-200/60 dark:border-slate-800/60 dark:bg-slate-900/80"
            >
                <slot />
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    &copy; {{ new Date().getFullYear() }} MonitorDuit. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </div>
</template>
