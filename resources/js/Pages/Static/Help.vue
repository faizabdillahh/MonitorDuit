<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Mail, MessageCircle, MapPin } from 'lucide-vue-next'
</script>

<template>
  <Head title="Bantuan & Kontak" />
  <AppLayout title="Bantuan & Kontak">
    <div class="max-w-4xl mx-auto space-y-6">
      
      <!-- Bantuan -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Pusat Bantuan</h1>
        <p class="text-slate-600 dark:text-slate-400 mb-8">Punya pertanyaan atau masalah? Kami siap membantu Anda.</p>
        
        <div class="space-y-4">
          <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-xl">
            <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Bagaimana cara kerja fitur Scan Struk?</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              Cukup ambil foto struk belanja Anda dan unggah di halaman Tambah Transaksi. Sistem AI kami akan secara otomatis mengekstrak total pembayaran dan nama merchant, lalu Anda hanya perlu menyimpannya!
            </p>
          </div>
          
          <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-xl">
            <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Apakah data saya aman?</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              Ya, seluruh data transaksi Anda dienkripsi dan hanya dapat diakses oleh Anda. Kami tidak akan membagikan data spesifik Anda kepada pihak ketiga manapun untuk tujuan pemasaran.
            </p>
          </div>
        </div>
      </div>

      <!-- Kontak -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Hubungi Kami</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="flex flex-col items-center p-6 bg-slate-50 dark:bg-slate-800 rounded-xl text-center">
            <div class="w-12 h-12 bg-brand-100 dark:bg-brand-500/20 text-brand-600 dark:text-brand-400 rounded-full flex items-center justify-center mb-4">
              <Mail class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Email</h3>
            <a href="mailto:support@monitorduit.com" class="text-brand-600 dark:text-brand-400 text-sm hover:underline">support@monitorduit.com</a>
          </div>

          <div class="flex flex-col items-center p-6 bg-slate-50 dark:bg-slate-800 rounded-xl text-center">
            <div class="w-12 h-12 bg-brand-100 dark:bg-brand-500/20 text-brand-600 dark:text-brand-400 rounded-full flex items-center justify-center mb-4">
              <MessageCircle class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Live Chat</h3>
            <p class="text-sm text-slate-500">Tersedia Senin-Jumat<br>09:00 - 17:00 WIB</p>
          </div>

          <div class="flex flex-col items-center p-6 bg-slate-50 dark:bg-slate-800 rounded-xl text-center">
            <div class="w-12 h-12 bg-brand-100 dark:bg-brand-500/20 text-brand-600 dark:text-brand-400 rounded-full flex items-center justify-center mb-4">
              <MapPin class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Kantor</h3>
            <p class="text-sm text-slate-500">Jakarta Pusat,<br>Indonesia</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
