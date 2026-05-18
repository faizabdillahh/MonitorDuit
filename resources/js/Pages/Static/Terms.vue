<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <Head title="Syarat & Ketentuan" />
  <AppLayout title="Syarat & Ketentuan">
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-6">Syarat & Ketentuan</h1>
      
      <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
        <p class="mb-4">Terakhir diperbarui: Mei 2026</p>
        
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">Penerimaan Ketentuan</h2>
        <p class="mb-4">
          Dengan mendaftar dan menggunakan layanan MonitorDuit, Anda menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan bagian mana pun dari syarat ini, Anda tidak diperkenankan untuk menggunakan layanan kami.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">Penggunaan Akun</h2>
        <p class="mb-4">
          Anda bertanggung jawab untuk menjaga kerahasiaan akun dan kata sandi Anda. MonitorDuit tidak bertanggung jawab atas segala kerugian yang timbul karena kelalaian Anda dalam menjaga akun Anda.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">Fitur AI</h2>
        <p class="mb-4">
          MonitorDuit menyediakan fitur "Scan Struk" yang menggunakan kecerdasan buatan. Kami tidak menjamin keakuratan 100% dari hasil ekstraksi data. Anda diwajibkan untuk meninjau kembali dan memverifikasi data sebelum menyimpannya ke dalam sistem.
        </p>

        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">Perubahan Ketentuan</h2>
        <p class="mb-4">
          Kami berhak untuk memperbarui syarat dan ketentuan ini sewaktu-waktu. Perubahan yang signifikan akan diberitahukan melalui aplikasi atau email terdaftar.
        </p>
      </div>
    </div>
  </AppLayout>
</template>
