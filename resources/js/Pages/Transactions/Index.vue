<script setup>
import { ref, computed, watch } from 'vue'
import { Link, Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Download, Search, SlidersHorizontal, X } from 'lucide-vue-next'

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
  <Head title="Transaksi" />
  <AppLayout>

    <!-- Search bar (Instagram explore style) -->
    <div class="mb-4">
      <div class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          v-model="search"
          type="text"
          placeholder="Cari..."
          class="w-full pl-10 pr-20 py-2.5 bg-gray-100 dark:bg-gray-900 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-700"
        />
        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
          <button v-if="hasActiveFilters" @click="resetFilters" class="p-1.5 text-gray-400 hover:text-red-500">
            <X class="w-4 h-4" />
          </button>
          <button @click="showFilters = !showFilters" :class="['p-1.5 rounded-lg transition-colors', showFilters || hasActiveFilters ? 'text-brand-600' : 'text-gray-400']">
            <SlidersHorizontal class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Filters panel -->
      <div v-if="showFilters" class="mt-3 p-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs text-gray-400 mb-1">Kategori</label>
            <select v-model="categoryFilter" @change="applyFilters" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500">
              <option value="">Semua</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Dari</label>
            <input v-model="dateFrom" @change="applyFilters" type="date" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Sampai</label>
            <input v-model="dateTo" @change="applyFilters" type="date" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
          </div>
        </div>
        <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
          <span class="text-xs text-gray-400">{{ transactions.total || 0 }} transaksi</span>
          <button @click="exportData('xlsx')" class="flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-700">
            <Download class="w-3.5 h-3.5" /> Export
          </button>
        </div>
      </div>
    </div>

    <!-- Categories horizontal scroll (Instagram stories-like) -->
    <div class="flex gap-3 overflow-x-auto pb-3 mb-4 -mx-4 px-4 scrollbar-hide">
      <button
        @click="categoryFilter = ''; applyFilters()"
        :class="[
          'flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors',
          !categoryFilter
            ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white'
            : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800',
        ]"
      >
        Semua
      </button>
      <button
        v-for="cat in categories"
        :key="cat.id"
        @click="categoryFilter = cat.id; applyFilters()"
        :class="[
          'flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap',
          categoryFilter == cat.id
            ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white'
            : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800',
        ]"
      >
        {{ cat.icon }} {{ cat.name }}
      </button>
    </div>

    <!-- Transaction Feed -->
    <div v-if="transactions.data && transactions.data.length > 0" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
      <Link
        v-for="tx in transactions.data"
        :key="tx.id"
        :href="route('transactions.show', tx.id)"
        class="flex items-center gap-3 px-4 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border-b border-gray-100 dark:border-gray-800 last:border-0"
      >
        <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-lg shrink-0">
          {{ tx.category?.icon || '📦' }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5">
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate">{{ tx.merchant_name || 'Transaksi' }}</p>
            <span v-if="tx.source === 'ai'" class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400">AI</span>
          </div>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ new Date(tx.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
            <span v-if="tx.category?.name"> · {{ tx.category.name }}</span>
          </p>
        </div>
        <div class="text-right shrink-0">
          <p class="text-[13px] font-semibold font-mono text-gray-900 dark:text-white">
            <span v-if="tx.currency !== 'IDR'" class="text-[10px] text-gray-400 mr-0.5">{{ tx.currency }}</span>
            {{ tx.currency !== 'IDR' ? Number(tx.total_amount).toLocaleString('id-ID') : formatCurrency(tx.total_amount) }}
          </p>
          <p v-if="tx.currency !== 'IDR'" class="text-[10px] text-gray-400 font-mono mt-0.5">≈ {{ formatCurrency(tx.amount_idr) }}</p>
        </div>
      </Link>
    </div>

    <!-- Pagination -->
    <div v-if="transactions.last_page > 1" class="flex justify-center gap-1 mt-5">
      <template v-for="link in transactions.links" :key="link.label">
        <Link v-if="link.url" :href="link.url" :class="['px-3 py-1.5 rounded-lg text-xs font-medium', link.active ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800']" v-html="link.label" preserve-state />
        <span v-else class="px-3 py-1.5 text-xs text-gray-300" v-html="link.label" />
      </template>
    </div>

    <!-- Empty State -->
    <div v-if="!transactions.data || transactions.data.length === 0" class="text-center py-16">
      <p class="text-sm text-gray-400">Belum ada transaksi</p>
      <Link :href="route('transactions.create')" class="inline-block mt-3 text-sm font-semibold text-brand-600">
        + Tambah transaksi pertama
      </Link>
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
