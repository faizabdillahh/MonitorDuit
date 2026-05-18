<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <Head title="Tentang Kami" />
  <AppLayout title="Tentang Kami">
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-6">Tentang MonitorDuit</h1>
      
      <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
        <p class="mb-4">
          MonitorDuit berawal dari sebuah ide sederhana: bagaimana kita bisa mengelola keuangan dengan lebih mudah, cerdas, dan tanpa harus dipusingkan oleh pencatatan manual yang membosankan?
        </p>
        <p class="mb-4">
          Dikembangkan untuk kalangan personal maupun UMKM, MonitorDuit hadir sebagai asisten keuangan pintar Anda. Melalui teknologi kecerdasan buatan (AI) yang kami integrasikan ke dalam fitur Scan Struk, Anda kini bisa mencatat setiap pengeluaran hanya dengan satu jepretan. AI kami akan mengekstrak total harga dan nama *merchant* secara otomatis.
        </p>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white mt-8 mb-4">Misi Kami</h2>
        <p class="mb-4">
          Membantu masyarakat Indonesia meraih kebebasan dan kedisiplinan finansial melalui teknologi pencatatan keuangan yang modern, mudah diakses, dan akurat.
        </p>
        <p>
          Terima kasih telah mempercayakan perjalanan finansial Anda bersama MonitorDuit. Dibuat dengan cinta untuk masa depan keuangan yang lebih baik.
        </p>
      </div>
    </div>
  </AppLayout>
</template>
