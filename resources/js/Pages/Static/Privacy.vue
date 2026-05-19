<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <Head title="Kebijakan Privasi" />
  <AppLayout title="Kebijakan Privasi">
    <div class="w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-6">Kebijakan Privasi</h1>
      
      <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
        <p class="mb-4">Terakhir diperbarui: Mei 2026</p>
        
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">1. Pengumpulan Data</h2>
        <p class="mb-4">
          Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami, seperti nama, alamat email, dan data transaksi keuangan (termasuk foto struk) yang Anda unggah.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">2. Penggunaan Data</h2>
        <p class="mb-4">
          Data yang dikumpulkan digunakan secara eksklusif untuk menyediakan, memelihara, dan meningkatkan layanan MonitorDuit. Kami menggunakan teknologi pemrosesan pihak ketiga (seperti API Google Gemini) secara terbatas hanya untuk mengekstrak teks dari struk Anda.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">3. Keamanan Data</h2>
        <p class="mb-4">
          Keamanan data finansial Anda adalah prioritas kami. Kami menggunakan standar enkripsi industri untuk melindungi akses tidak sah ke akun Anda. Anda bertanggung jawab untuk menjaga kerahasiaan kata sandi Anda.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">4. Hak Anda</h2>
        <p class="mb-4">
          Anda berhak untuk mengakses, mengubah, atau menghapus data pribadi dan transaksi Anda kapan saja langsung melalui menu pengaturan di dalam aplikasi.
        </p>
      </div>
    </div>
  </AppLayout>
</template>
