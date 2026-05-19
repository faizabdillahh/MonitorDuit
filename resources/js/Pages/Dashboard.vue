<script setup>
import { computed } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip } from 'chart.js'
import { ArrowRight, AlertTriangle, XCircle, Camera } from 'lucide-vue-next'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip)

const props = defineProps({
  summary: Object,
  weeklyTrend: Array,
  recentTransactions: Array,
  categories: Array,
  budgetWidgets: Array,
})

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

const chartData = computed(() => ({
  labels: props.weeklyTrend.map(d => d.label),
  datasets: [{
    data: props.weeklyTrend.map(d => d.amount),
    backgroundColor: 'rgba(16, 185, 129, 0.15)',
    borderColor: '#10b981',
    borderWidth: 1.5,
    borderRadius: 6,
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
    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } }
  }
}
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout>

    <!-- Stories-like: Summary Cards (horizontal scroll on mobile) -->
    <div class="flex gap-3 overflow-x-auto pb-1 mb-5 -mx-4 px-4 scrollbar-hide">
      <div class="flex-shrink-0 w-[160px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Bulan ini</p>
        <p class="text-lg font-bold font-mono text-gray-900 dark:text-white mt-1.5 truncate">{{ formatCurrency(summary.monthly_total) }}</p>
      </div>
      <div class="flex-shrink-0 w-[140px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Transaksi</p>
        <p class="text-lg font-bold font-mono text-gray-900 dark:text-white mt-1.5">{{ summary.monthly_count }}</p>
      </div>
      <div class="flex-shrink-0 w-[160px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
        <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Rata-rata/hari</p>
        <p class="text-lg font-bold font-mono text-gray-900 dark:text-white mt-1.5 truncate">{{ formatCurrency(summary.daily_average) }}</p>
      </div>
      <!-- Scan CTA card -->
      <Link :href="route('transactions.create')" class="flex-shrink-0 w-[120px] bg-gray-900 dark:bg-white border border-gray-800 dark:border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-2 hover:opacity-90 transition-opacity">
        <Camera class="w-6 h-6 text-white dark:text-gray-900" />
        <span class="text-xs font-semibold text-white dark:text-gray-900">Scan Struk</span>
      </Link>
    </div>

    <!-- Chart Card (Instagram post-like) -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl mb-4 overflow-hidden">
      <div class="px-4 py-3 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Tren 7 hari</span>
      </div>
      <div class="px-4 pb-4 h-40">
        <Bar :data="chartData" :options="chartOptions" />
      </div>
    </div>

    <!-- Budget Widget (Instagram post-like) -->
    <div v-if="budgetWidgets && budgetWidgets.length > 0" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl mb-4 overflow-hidden">
      <div class="px-4 py-3 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Budget bulan ini</span>
        <Link :href="route('budgets.index')" class="text-xs font-semibold text-brand-600 dark:text-brand-400">Lihat semua</Link>
      </div>
      <div class="divide-y divide-gray-50 dark:divide-gray-800">
        <div v-for="item in budgetWidgets.slice(0, 4)" :key="item.budget.id" class="px-4 py-3">
          <div class="flex items-center justify-between mb-1.5">
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="text-base">{{ item.budget.category?.icon }}</span>
              <span class="text-[13px] font-medium text-gray-900 dark:text-white truncate">{{ item.budget.category?.name }}</span>
              <AlertTriangle v-if="item.status === 'warning'" class="w-3.5 h-3.5 text-amber-500 shrink-0" />
              <XCircle v-if="item.status === 'exceeded'" class="w-3.5 h-3.5 text-red-500 shrink-0" />
            </div>
            <span class="text-xs font-mono" :class="{
              'text-gray-400': item.status === 'normal',
              'text-amber-500': item.status === 'warning',
              'text-red-500': item.status === 'exceeded',
            }">{{ item.percentage }}%</span>
          </div>
          <div class="h-[3px] bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="{
                'bg-brand-500': item.status === 'normal',
                'bg-amber-500': item.status === 'warning',
                'bg-red-500': item.status === 'exceeded',
              }"
              :style="{ width: Math.min(item.percentage, 100) + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions (Feed-style) -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
      <div class="px-4 py-3 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Transaksi terbaru</span>
        <Link :href="route('transactions.index')" class="text-xs font-semibold text-brand-600 dark:text-brand-400 flex items-center gap-0.5">
          Semua <ArrowRight class="w-3 h-3" />
        </Link>
      </div>

      <div v-if="recentTransactions.length > 0">
        <Link
          v-for="tx in recentTransactions"
          :key="tx.id"
          :href="route('transactions.show', tx.id)"
          class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border-b border-gray-50 dark:border-gray-800/50 last:border-0"
        >
          <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-lg shrink-0">
            {{ tx.category?.icon || '📦' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate">
              {{ tx.merchant_name || tx.category?.name || 'Transaksi' }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
              {{ new Date(tx.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
              <span v-if="tx.category?.name" class="ml-1">· {{ tx.category.name }}</span>
            </p>
          </div>
          <span class="text-[13px] font-semibold font-mono text-gray-900 dark:text-white whitespace-nowrap">
            {{ formatCurrency(tx.total_amount) }}
          </span>
        </Link>
      </div>

      <div v-else class="text-center py-10 px-4">
        <p class="text-sm text-gray-400">Belum ada transaksi</p>
        <Link :href="route('transactions.create')" class="inline-block mt-2 text-sm font-semibold text-brand-600">+ Tambah transaksi</Link>
      </div>
    </div>

  </AppLayout>
</template>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
