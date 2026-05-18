<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, ArcElement, Legend } from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, ArcElement, Legend)

const props = defineProps({
  summary: Object,
  currentYear: Number,
  currentMonth: Number,
})

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

function changeMonth(delta) {
  let m = props.currentMonth + delta
  let y = props.currentYear
  if (m > 12) { m = 1; y += 1 }
  if (m < 1) { m = 12; y -= 1 }
  router.get(route('statistics'), { year: y, month: m }, { preserveState: true })
}

const isCurrentMonth = computed(() => {
  const now = new Date()
  return props.currentYear === now.getFullYear() && props.currentMonth === now.getMonth() + 1
})

const barData = computed(() => ({
  labels: props.summary.daily_totals.map(d => d.day),
  datasets: [{
    data: props.summary.daily_totals.map(d => d.amount),
    backgroundColor: 'rgba(16, 185, 129, 0.2)',
    borderColor: '#10b981',
    borderWidth: 2,
    borderRadius: 4,
  }]
}))

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => formatCurrency(ctx.raw) } } },
  scales: {
    y: { display: false },
    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
  }
}

const donutData = computed(() => ({
  labels: props.summary.category_breakdown.map(c => c.category_name),
  datasets: [{
    data: props.summary.category_breakdown.map(c => c.total_amount),
    backgroundColor: props.summary.category_breakdown.map(c => c.category_color),
    borderWidth: 0,
  }]
}))

const donutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '70%',
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => formatCurrency(ctx.raw) } } },
}
</script>

<template>
  <Head title="Statistik" />
  <AppLayout title="Statistik">
    <!-- Month Navigation -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
      <button @click="changeMonth(-1)" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <div class="text-center">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ summary.month_name }}</h2>
      </div>
      <button @click="changeMonth(1)" :disabled="isCurrentMonth" :class="['p-2 rounded-xl transition-colors', isCurrentMonth ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-800']">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 relative overflow-hidden">
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Pengeluaran</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ formatCurrency(summary.total_amount) }}</p>
        
        <div v-if="summary.change_percent !== null" class="mt-2 flex items-center gap-1 text-sm">
          <span v-if="summary.change_percent > 0" class="text-red-500 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" /></svg>
            {{ summary.change_percent }}%
          </span>
          <span v-else-if="summary.change_percent < 0" class="text-emerald-500 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" /></svg>
            {{ Math.abs(summary.change_percent) }}%
          </span>
          <span class="text-slate-400">vs bulan lalu</span>
        </div>
      </div>
      
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 relative overflow-hidden">
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Rata-rata Harian</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ formatCurrency(summary.daily_average) }}</p>
        <p class="mt-2 text-sm text-slate-400">Berdasarkan {{ summary.total_count }} transaksi</p>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 relative overflow-hidden">
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Kategori Terbanyak</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ summary.top_category || '-' }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Daily Bar Chart -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Pengeluaran Harian</h3>
        <div class="h-64">
          <Bar :data="barData" :options="barOptions" />
        </div>
      </div>

      <!-- Category Donut -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Proporsi Kategori</h3>
        <div class="h-48 relative">
          <Doughnut v-if="summary.category_breakdown.length > 0" :data="donutData" :options="donutOptions" />
          <div v-else class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">Tidak ada data</div>
        </div>
        
        <div class="mt-6 space-y-3" v-if="summary.category_breakdown.length > 0">
          <div v-for="cat in summary.category_breakdown.slice(0, 5)" :key="cat.category_id" class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: cat.category_color }"></span>
              <span class="text-sm text-slate-700 dark:text-slate-300">{{ cat.category_name }}</span>
            </div>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ formatCurrency(cat.total_amount) }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
