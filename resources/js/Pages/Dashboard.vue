<script setup>
import { computed } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip } from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip)

const props = defineProps({
  summary: Object,
  weeklyTrend: Array,
  recentTransactions: Array,
  categories: Array,
})

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

const chartData = computed(() => ({
  labels: props.weeklyTrend.map(d => d.label),
  datasets: [{
    data: props.weeklyTrend.map(d => d.amount),
    backgroundColor: 'rgba(16, 185, 129, 0.2)',
    borderColor: '#10b981',
    borderWidth: 2,
    borderRadius: 8,
    borderSkipped: false,
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: {
    callbacks: { label: (ctx) => formatCurrency(ctx.raw) }
  }},
  scales: {
    y: { display: false },
    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
  }
}

const summaryCards = computed(() => [
  { label: 'Total Bulan Ini', value: formatCurrency(props.summary.monthly_total), icon: '💰', color: 'from-emerald-500 to-teal-500' },
  { label: 'Jumlah Transaksi', value: props.summary.monthly_count, icon: '📝', color: 'from-blue-500 to-indigo-500' },
  { label: 'Rata-rata/Hari', value: formatCurrency(props.summary.daily_average), icon: '📊', color: 'from-purple-500 to-pink-500' },
])
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout title="Dashboard">
    <!-- Greeting -->
    <div class="mb-8">
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white">
        Halo, {{ $page.props.auth.user.name }}! 👋
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1">Pantau pengeluaranmu hari ini</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div
        v-for="card in summaryCards"
        :key="card.label"
        class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 group hover:shadow-lg transition-all duration-300"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ card.label }}</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ card.value }}</p>
          </div>
          <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center text-lg', card.color]">
            {{ card.icon }}
          </div>
        </div>
        <div :class="['absolute bottom-0 left-0 h-1 bg-gradient-to-r w-full opacity-0 group-hover:opacity-100 transition-opacity', card.color]"></div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Weekly Chart -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-slate-900 dark:text-white">Tren 7 Hari Terakhir</h2>
        </div>
        <div class="h-48">
          <Bar :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="font-semibold text-slate-900 dark:text-white mb-4">Aksi Cepat</h2>
        <div class="space-y-3">
          <Link
            :href="route('transactions.create')"
            class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 group"
          >
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
              <p class="font-semibold text-sm">Scan Struk</p>
              <p class="text-xs text-emerald-100">Foto & catat otomatis</p>
            </div>
          </Link>

          <Link
            :href="route('transactions.index')"
            class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
          >
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
              📝
            </div>
            <div>
              <p class="font-semibold text-sm">Riwayat</p>
              <p class="text-xs text-slate-500">Lihat semua transaksi</p>
            </div>
          </Link>

          <Link
            :href="route('statistics')"
            class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
          >
            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
              📈
            </div>
            <div>
              <p class="font-semibold text-sm">Statistik</p>
              <p class="text-xs text-slate-500">Analisis pengeluaran</p>
            </div>
          </Link>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mt-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-900 dark:text-white">Transaksi Terbaru</h2>
        <Link :href="route('transactions.index')" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 font-medium">
          Lihat Semua →
        </Link>
      </div>

      <div v-if="recentTransactions.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="tx in recentTransactions"
          :key="tx.id"
          class="flex items-center justify-between py-3 first:pt-0 last:pb-0"
        >
          <Link :href="route('transactions.show', tx.id)" class="flex items-center gap-3 min-w-0 flex-1 group">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0" :style="{ backgroundColor: tx.category?.color + '15' }">
              {{ tx.category?.icon || '📦' }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-medium text-slate-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                {{ tx.merchant_name || tx.category?.name || 'Transaksi' }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ new Date(tx.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
              </p>
            </div>
          </Link>
          <span class="text-sm font-semibold text-slate-900 dark:text-white whitespace-nowrap ml-3">
            {{ formatCurrency(tx.total_amount) }}
          </span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="text-4xl mb-3">🧾</div>
        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada transaksi</p>
        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Mulai dengan scan struk pertamamu!</p>
        <Link
          :href="route('transactions.create')"
          class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-colors"
        >
          Scan Struk Sekarang
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
