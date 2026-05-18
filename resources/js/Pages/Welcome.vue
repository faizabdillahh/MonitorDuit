<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans selection:bg-emerald-500 selection:text-white text-slate-900 dark:text-white">
        <!-- Navigation -->
        <nav class="absolute w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                        <span class="text-white font-bold text-lg">M</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight">MonitorDuit</span>
                </div>
                <div v-if="canLogin" class="flex items-center gap-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="text-sm font-semibold hover:text-emerald-500 transition-colors">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-sm font-medium hover:text-emerald-500 transition-colors">
                            Masuk
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold rounded-full hover:scale-105 transition-transform">
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative flex flex-col items-center justify-center min-h-screen px-6 pt-20 pb-16 text-center overflow-hidden">
            
            <!-- Background Glows -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/20 dark:bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none"></div>
            <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-teal-500/10 dark:bg-teal-500/5 blur-[100px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-sm font-semibold mb-8 border border-emerald-200 dark:border-emerald-500/20">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Kini dengan AI Scanner
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-tight">
                    Catat Pengeluaran,<br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-600">Cukup Foto Struknya.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Tinggalkan cara lama mencatat pengeluaran secara manual. MonitorDuit menggunakan AI untuk membaca struk belanja Anda otomatis dalam hitungan detik.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-2xl shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all text-lg">
                        Buka Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('register')" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-2xl shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all text-lg">
                            Mulai Sekarang — Gratis
                        </Link>
                        <Link :href="route('login')" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-white font-bold rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-lg">
                            Sudah Punya Akun?
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 mt-24 max-w-5xl mx-auto w-full text-left">
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
                        📸
                    </div>
                    <h3 class="text-xl font-bold mb-3">Scan Pintar AI</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Foto struk belanja Anda dan AI kami akan langsung mengekstrak total harga, tanggal, dan nama toko secara instan.
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
                        📊
                    </div>
                    <h3 class="text-xl font-bold mb-3">Analitik Detail</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Pantau kemana perginya uang Anda dengan visualisasi grafik interaktif mingguan dan bulanan yang memanjakan mata.
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-14 h-14 bg-purple-50 dark:bg-purple-500/10 text-purple-500 rounded-2xl flex items-center justify-center text-2xl mb-6">
                        🌙
                    </div>
                    <h3 class="text-xl font-bold mb-3">Tampilan Premium</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Didesain dengan indah, responsif di semua layar, dan mendukung Mode Gelap sepenuhnya demi kenyamanan mata Anda.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>
