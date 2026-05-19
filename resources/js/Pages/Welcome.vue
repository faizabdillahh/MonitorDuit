<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Camera, BarChart2, Moon } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="min-h-screen bg-[#fafafa] dark:bg-black font-sans text-gray-900 dark:text-gray-100">
        <!-- Navigation -->
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-5xl mx-auto px-4 h-[60px] flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                        <span class="text-white font-bold text-xs">M</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight hidden sm:block" style="font-family: 'Plus Jakarta Sans', sans-serif;">MonitorDuit</span>
                </div>
                <div v-if="canLogin" class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="text-sm font-semibold hover:text-brand-600 transition-colors">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-sm font-medium hover:text-brand-600 transition-colors px-3 py-1.5">
                            Masuk
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Daftar
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex flex-col items-center justify-center pt-10 pb-16 px-4 text-center">
            
            <div class="max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-brand-600 dark:text-brand-400 text-xs font-semibold mb-8">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    Kini dengan AI Receipt Scanner
                </div>
                
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                    Catat Pengeluaran,<br />
                    <span class="text-brand-600 dark:text-brand-400">Cukup Foto Struknya.</span>
                </h1>
                
                <p class="text-base md:text-lg text-gray-500 dark:text-gray-400 mb-10 max-w-xl mx-auto leading-relaxed">
                    Tinggalkan cara lama mencatat pengeluaran secara manual. MonitorDuit menggunakan AI untuk membaca struk belanja Anda otomatis dalam hitungan detik.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="w-full sm:w-auto px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-colors">
                        Buka Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('register')" class="w-full sm:w-auto px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-colors">
                            Mulai Sekarang — Gratis
                        </Link>
                        <Link :href="route('login')" class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Sudah Punya Akun?
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-16 sm:mt-24 max-w-5xl mx-auto w-full text-left px-4">
                <!-- Feature 1 -->
                <div class="bg-white dark:bg-gray-900 p-5 sm:p-6 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-brand-300 dark:hover:border-brand-700 transition-colors">
                    <div class="w-12 h-12 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded-xl flex items-center justify-center mb-5 sm:mb-6">
                        <Camera class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Scan Pintar AI</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                        Foto struk belanja Anda dan AI kami akan langsung mengekstrak total harga, tanggal, dan nama toko secara instan.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white dark:bg-gray-900 p-5 sm:p-6 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-brand-300 dark:hover:border-brand-700 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-5 sm:mb-6">
                        <BarChart2 class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Analitik Visual</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                        Pantau kemana perginya uang Anda dengan visualisasi grafik interaktif mingguan dan bulanan yang memanjakan mata.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white dark:bg-gray-900 p-5 sm:p-6 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-brand-300 dark:hover:border-brand-700 transition-colors">
                    <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center mb-5 sm:mb-6">
                        <Moon class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Desain Premium</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                        Didesain dengan sangat indah, responsif di semua ukuran layar, dan mendukung Mode Gelap sepenuhnya.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 mt-12">
            <div class="max-w-5xl mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                            <span class="text-white font-bold text-[10px]">M</span>
                        </div>
                        <span class="text-gray-900 dark:text-white font-semibold text-sm">MonitorDuit</span>
                    </div>
                    
                    <div class="flex flex-wrap gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                        <Link :href="route('about')" class="hover:text-brand-600 transition-colors">Tentang Kami</Link>
                        <Link :href="route('help')" class="hover:text-brand-600 transition-colors">Bantuan</Link>
                        <Link :href="route('privacy')" class="hover:text-brand-600 transition-colors">Privasi</Link>
                        <Link :href="route('terms')" class="hover:text-brand-600 transition-colors">Syarat Ketentuan</Link>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-400">
                    <p>&copy; {{ new Date().getFullYear() }} MonitorDuit.</p>
                    <p>Versi 1.2.0</p>
                </div>
            </div>
        </footer>
    </div>
</template>
