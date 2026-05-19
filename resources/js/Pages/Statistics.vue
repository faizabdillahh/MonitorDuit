<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, ArcElement, Legend } from 'chart.js'
import { ChevronLeft, ChevronRight, TrendingUp, TrendingDown } from 'lucide-vue-next'

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
    <!-- Header -->
    <div class="mb-5">
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Statistik</h1>
      <p class="text-sm text-gray-500 mt-0.5">Analisis pengeluaran bulanan</p>
    </div>

    <!-- Month selector -->
    <div class="flex items-center justify-center gap-3 mb-6">
      <button @click="changeMonth(-1)" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors">
        <ChevronLeft class="w-4 h-4" />
      </button>
      <span class="text-sm font-medium text-gray-900 dark:text-white min-w-[160px] text-center">{{ summary.month_name }}</span>
      <button @click="changeMonth(1)" :disabled="isCurrentMonth" :class="['p-1.5 rounded-md transition-colors', isCurrentMonth ? 'opacity-30 cursor-not-allowed text-gray-400' : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400']">
        <ChevronRight class="w-4 h-4" />
      </button>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-3 gap-3 mb-4">
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Total</p>
        <p class="text-base sm:text-lg font-bold font-mono text-gray-900 dark:text-white mt-1 truncate">{{ formatCurrency(summary.total_amount) }}</p>
        <div v-if="summary.change_percent !== null" class="mt-1 flex items-center gap-1 text-xs">
          <span v-if="summary.change_percent > 0" class="text-red-500 flex items-center gap-0.5"><TrendingUp class="w-3 h-3" />{{ summary.change_percent }}%</span>
          <span v-else-if="summary.change_percent < 0" class="text-brand-500 flex items-center gap-0.5"><TrendingDown class="w-3 h-3" />{{ Math.abs(summary.change_percent) }}%</span>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Rata-rata</p>
        <p class="text-base sm:text-lg font-bold font-mono text-gray-900 dark:text-white mt-1 truncate">{{ formatCurrency(summary.daily_average) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Top</p>
        <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mt-1 truncate">{{ summary.top_category || '-' }}</p>
      </div>
    </div>

    <!-- Daily Chart -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl mb-4 overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Pengeluaran harian</span>
      </div>
      <div class="px-4 pb-4 pt-2 h-56">
        <Bar :data="barData" :options="barOptions" />
      </div>
    </div>

    <!-- Category Donut -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Proporsi kategori</span>
      </div>
      <div class="p-4">
        <div class="h-44 relative">
          <Doughnut v-if="summary.category_breakdown.length > 0" :data="donutData" :options="donutOptions" />
          <div v-else class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">Tidak ada data</div>
        </div>
        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-800" v-if="summary.category_breakdown.length > 0">
          <div v-for="cat in summary.category_breakdown.slice(0, 5)" :key="cat.category_id" class="flex items-center justify-between py-2.5">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: cat.category_color }"></span>
              <span class="text-[13px] text-gray-700 dark:text-gray-300">{{ cat.category_name }}</span>
            </div>
            <span class="text-[13px] font-semibold font-mono text-gray-900 dark:text-white">{{ formatCurrency(cat.total_amount) }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

