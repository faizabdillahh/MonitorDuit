<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useToast } from '@/Composables/useToast'
import { Plus, Edit2, Trash2, ChevronLeft, ChevronRight, X, AlertTriangle, XCircle } from 'lucide-vue-next'

const props = defineProps({
  budgets: Array,
  categories: Array,
  filters: Object,
})

const { show, error } = useToast()

const showModal = ref(false)
const editingBudget = ref(null)
const confirmingBudgetDeletion = ref(false)
const budgetToDelete = ref(null)

const form = useForm({
  category_id: '',
  amount: '',
  is_recurring: true,
  month: null,
  year: null,
})

const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

const currentMonthLabel = computed(() => {
  return `${monthNames[props.filters.month - 1]} ${props.filters.year}`
})

const availableCategories = computed(() => {
  const budgetCatIds = props.budgets.map(b => b.budget.category_id)
  return props.categories.filter(c => !budgetCatIds.includes(c.id))
})

function openAddModal() {
  editingBudget.value = null
  form.reset()
  form.category_id = availableCategories.value[0]?.id || ''
  form.is_recurring = true
  form.month = null
  form.year = null
  showModal.value = true
}

function openEditModal(budgetData) {
  editingBudget.value = budgetData.budget
  form.category_id = budgetData.budget.category_id
  form.amount = budgetData.budget.amount
  form.is_recurring = budgetData.budget.is_recurring
  form.month = budgetData.budget.month
  form.year = budgetData.budget.year
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingBudget.value = null
  form.reset()
}

function submitBudget() {
  if (editingBudget.value) {
    form.put(route('budgets.update', editingBudget.value.id), {
      preserveScroll: true,
      onSuccess: () => { closeModal(); show('Budget berhasil diperbarui!') },
    })
  } else {
    form.post(route('budgets.store'), {
      preserveScroll: true,
      onSuccess: () => { closeModal(); show('Budget berhasil ditambahkan!') },
    })
  }
}

function confirmDeleteBudget(id) {
  budgetToDelete.value = id
  confirmingBudgetDeletion.value = true
}

function executeDeleteBudget() {
  if (budgetToDelete.value) {
    router.delete(route('budgets.destroy', budgetToDelete.value), {
      preserveScroll: true,
      onSuccess: () => {
        confirmingBudgetDeletion.value = false
        budgetToDelete.value = null
        show('Budget berhasil dihapus!')
      },
    })
  }
}

function cancelDeleteBudget() {
  confirmingBudgetDeletion.value = false
  budgetToDelete.value = null
}

function prevMonth() {
  let m = props.filters.month - 1
  let y = props.filters.year
  if (m < 1) { m = 12; y-- }
  router.get(route('budgets.index'), { month: m, year: y }, { preserveState: true })
}

function nextMonth() {
  let m = props.filters.month + 1
  let y = props.filters.year
  if (m > 12) { m = 1; y++ }
  router.get(route('budgets.index'), { month: m, year: y }, { preserveState: true })
}

function formatRp(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID')
}
</script>

<template>
  <Head title="Budget" />
  <AppLayout title="Budget">
    <div class="w-full">

      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Budget</h1>
          <p class="text-sm text-gray-500 mt-0.5">Batas pengeluaran per kategori</p>
        </div>
        <button @click="openAddModal" class="flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
          <Plus class="w-3.5 h-3.5" /> Tambah
        </button>
      </div>

      <!-- Month Selector -->
      <div class="flex items-center justify-center gap-3 mb-6">
        <button @click="prevMonth" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors">
          <ChevronLeft class="w-4 h-4" />
        </button>
        <span class="text-sm font-medium text-gray-900 dark:text-white min-w-[160px] text-center">{{ currentMonthLabel }}</span>
        <button @click="nextMonth" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors">
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>

      <!-- Empty State -->
      <div v-if="budgets.length === 0" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-10 text-center">
        <p class="text-sm text-gray-400">Belum ada budget untuk bulan ini</p>
        <button @click="openAddModal" class="mt-3 text-sm text-brand-600 hover:text-brand-700 font-medium">+ Buat budget pertama</button>
      </div>

      <!-- Budget List -->
      <div v-else class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg divide-y divide-gray-100 dark:divide-gray-800">
        <div
          v-for="item in budgets"
          :key="item.budget.id"
          class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group"
        >
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-2">
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="text-lg shrink-0">{{ item.budget.category?.icon }}</span>
              <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                  <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ item.budget.category?.name }}</span>
                  <AlertTriangle v-if="item.status === 'warning'" class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                  <XCircle v-if="item.status === 'exceeded'" class="w-3.5 h-3.5 text-red-500 shrink-0" />
                </div>
                <span class="text-xs text-gray-400">{{ item.budget.is_recurring ? 'Setiap bulan' : 'Bulan ini saja' }}</span>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
              <span class="text-xs font-mono" :class="{
                'text-brand-600': item.status === 'normal',
                'text-amber-600': item.status === 'warning',
                'text-red-600': item.status === 'exceeded',
              }">{{ formatRp(item.spent) }} / {{ formatRp(item.budget.amount) }}</span>
              <div class="flex items-center gap-0.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                <button @click="openEditModal(item)" class="p-2 sm:p-1.5 text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-md"><Edit2 class="w-4 h-4 sm:w-3.5 sm:h-3.5" /></button>
                <button @click="confirmDeleteBudget(item.budget.id)" class="p-2 sm:p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-md"><Trash2 class="w-4 h-4 sm:w-3.5 sm:h-3.5" /></button>
              </div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
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
              <span class="text-xs font-mono w-10 text-right" :class="{
                'text-brand-600': item.status === 'normal',
                'text-amber-600': item.status === 'warning',
                'text-red-600': item.status === 'exceeded',
              }">{{ item.percentage }}%</span>
            </div>
            <div v-if="item.projected > 0" class="text-[10px] text-gray-400 pl-1">
              *Telah dicadangkan {{ formatRp(item.projected) }} untuk transaksi rutin (recurring) yang akan datang.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
      <Transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center">
          <div class="fixed inset-0 bg-black/50" @click="closeModal"></div>

          <div class="relative bg-white dark:bg-gray-900 w-full sm:max-w-md sm:rounded-xl rounded-t-2xl border-t sm:border border-gray-200 dark:border-gray-800 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="sm:hidden flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-300 dark:bg-gray-700 rounded-full"></div></div>
            
            <div class="px-5 py-4">
              <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ editingBudget ? 'Edit Budget' : 'Tambah Budget' }}</h3>
                <button @click="closeModal" class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"><X class="w-4 h-4 text-gray-400" /></button>
              </div>

            <form @submit.prevent="submitBudget" class="space-y-4">
              <div v-if="!editingBudget">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                <select v-model="form.category_id" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent" required>
                  <option value="" disabled>Pilih kategori</option>
                  <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batas Budget</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                  <input v-model="form.amount" type="number" step="1" min="1" class="w-full pl-9 pr-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent font-mono" placeholder="1.500.000" required />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Berlaku</label>
                <div class="grid grid-cols-2 gap-2">
                  <label :class="['cursor-pointer p-2.5 border rounded-lg text-center text-xs font-medium transition-colors', form.is_recurring ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800']">
                    <input type="radio" :value="true" v-model="form.is_recurring" class="hidden" />
                    Setiap Bulan
                  </label>
                  <label :class="['cursor-pointer p-2.5 border rounded-lg text-center text-xs font-medium transition-colors', !form.is_recurring ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800']">
                    <input type="radio" :value="false" v-model="form.is_recurring" class="hidden" />
                    Bulan Tertentu
                  </label>
                </div>
              </div>

              <div v-if="!form.is_recurring" class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                  <select v-model="form.month" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" required>
                    <option v-for="(name, idx) in monthNames" :key="idx" :value="idx + 1">{{ name }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                  <input v-model="form.year" type="number" min="2024" class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 font-mono" required />
                </div>
              </div>

              <div class="flex gap-2 pt-2">
                <button type="button" @click="closeModal" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button type="submit" :disabled="form.processing" class="flex-1 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg disabled:opacity-50 transition-colors">{{ editingBudget ? 'Simpan' : 'Tambah' }}</button>
              </div>
            </form>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirmation -->
    <Teleport to="body">
      <Transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="confirmingBudgetDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50" @click="cancelDeleteBudget"></div>
          <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xl w-full max-w-xs p-5 text-center">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Hapus budget?</h3>
            <p class="text-sm text-gray-400 mb-5">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2">
              <button @click="cancelDeleteBudget" class="flex-1 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:bg-gray-800 rounded-lg">Batal</button>
              <button @click="executeDeleteBudget" class="flex-1 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg">Hapus</button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
