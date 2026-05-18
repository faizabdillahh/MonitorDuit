<script setup>
import { ref, computed, watch } from 'vue'
import { Link, Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  transactions: Object,
  categories: Array,
  filters: Object,
})

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

const search = ref(props.filters?.search || '')
const categoryFilter = ref(props.filters?.category || '')
const dateFrom = ref(props.filters?.date_from || '')
const dateTo = ref(props.filters?.date_to || '')
const showFilters = ref(false)

let searchTimeout = null
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => applyFilters(), 300)
})

function applyFilters() {
  const params = {}
  if (search.value) params.search = search.value
  if (categoryFilter.value) params.category = categoryFilter.value
  if (dateFrom.value) params.date_from = dateFrom.value
  if (dateTo.value) params.date_to = dateTo.value
  router.get(route('transactions.index'), params, { preserveState: true, replace: true })
}

function resetFilters() {
  search.value = ''; categoryFilter.value = ''; dateFrom.value = ''; dateTo.value = ''
  router.get(route('transactions.index'), {}, { preserveState: true, replace: true })
}

const hasActiveFilters = computed(() => search.value || categoryFilter.value || dateFrom.value || dateTo.value)

function exportData(format) {
  const params = new URLSearchParams()
  if (dateFrom.value) params.append('date_from', dateFrom.value)
  if (dateTo.value) params.append('date_to', dateTo.value)
  if (categoryFilter.value) params.append('category', categoryFilter.value)
  params.append('format', format)
  window.location.href = route('export') + '?' + params.toString()
}
</script>

<template>
  <Head title="Riwayat Transaksi" />
  <AppLayout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Pengeluaran</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ transactions.total || 0 }} transaksi</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="exportData('xlsx')" class="px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">📥 Export</button>
        <Link :href="route('transactions.create')" class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg shadow-emerald-500/20">+ Scan Struk</Link>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="flex-1 relative">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" type="text" placeholder="Cari merchant atau catatan..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none" />
        </div>
        <button @click="showFilters = !showFilters" :class="['px-3 py-2.5 rounded-xl border text-sm font-medium transition-colors', hasActiveFilters ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300']">🔍 Filter</button>
        <button v-if="hasActiveFilters" @click="resetFilters" class="px-3 py-2.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium">✕</button>
      </div>
      <div v-if="showFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
        <div>
          <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Kategori</label>
          <select v-model="categoryFilter" @change="applyFilters" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Dari Tanggal</label>
          <input v-model="dateFrom" @change="applyFilters" type="date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sampai Tanggal</label>
          <input v-model="dateTo" @change="applyFilters" type="date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
      </div>
    </div>

    <!-- List -->
    <div v-if="transactions.data && transactions.data.length > 0" class="space-y-3">
      <Link v-for="tx in transactions.data" :key="tx.id" :href="route('transactions.show', tx.id)" class="flex items-center gap-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30 transition-all group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0" :style="{ backgroundColor: (tx.category?.color || '#6b7280') + '15' }">{{ tx.category?.icon || '📦' }}</div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ tx.merchant_name || 'Transaksi' }}</p>
            <span v-if="tx.source === 'ai'" class="px-1.5 py-0.5 text-[10px] font-bold rounded-md bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400">AI</span>
          </div>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ new Date(tx.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
            <span class="text-xs px-1.5 py-0.5 rounded-md" :style="{ backgroundColor: (tx.category?.color || '#6b7280') + '15', color: tx.category?.color }">{{ tx.category?.name }}</span>
          </div>
        </div>
        <p class="text-sm font-bold text-slate-900 dark:text-white shrink-0">{{ formatCurrency(tx.total_amount) }}</p>
      </Link>

      <!-- Pagination -->
      <div v-if="transactions.last_page > 1" class="flex justify-center gap-1 mt-6">
        <template v-for="link in transactions.links" :key="link.label">
          <Link v-if="link.url" :href="link.url" :class="['px-3 py-2 rounded-lg text-sm font-medium', link.active ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700']" v-html="link.label" preserve-state />
          <span v-else class="px-3 py-2 text-sm text-slate-400" v-html="link.label" />
        </template>
      </div>
    </div>

    <!-- Empty -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-16 text-center">
      <div class="text-5xl mb-4">🧾</div>
      <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Belum ada transaksi</h3>
      <p class="text-slate-500 dark:text-slate-400 mt-2">Mulai catat pengeluaranmu dengan scan struk!</p>
      <Link :href="route('transactions.create')" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-emerald-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-emerald-500/20">📸 Scan Struk</Link>
    </div>
  </AppLayout>
</template>
