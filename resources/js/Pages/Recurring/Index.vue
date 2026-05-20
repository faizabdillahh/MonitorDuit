<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useToast } from '@/Composables/useToast'
import { Plus, Edit2, Trash2, X, ToggleLeft, ToggleRight, AlertTriangle } from 'lucide-vue-next'

const props = defineProps({
  recurrings: Array,
  categories: Array,
  frequencies: Array,
})

const { show } = useToast()

const showModal = ref(false)
const editingItem = ref(null)
const confirmingDeletion = ref(false)
const itemToDelete = ref(null)

const form = useForm({
  category_id: '',
  name: '',
  merchant_name: '',
  amount: '',
  frequency: 'monthly',
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  notes: '',
})

function openAddModal() {
  editingItem.value = null
  form.reset()
  form.category_id = props.categories[0]?.id || ''
  form.name = ''
  form.start_date = new Date().toISOString().split('T')[0]
  form.frequency = 'monthly'
  showModal.value = true
}

function openEditModal(item) {
  editingItem.value = item
  form.category_id = item.category_id
  form.name = item.name || ''
  form.merchant_name = item.merchant_name || ''
  form.amount = item.amount
  form.frequency = item.frequency
  form.end_date = item.end_date || ''
  form.notes = item.notes || ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingItem.value = null
  form.reset()
}

function submitForm() {
  if (editingItem.value) {
    form.put(route('recurring.update', editingItem.value.id), {
      preserveScroll: true,
      onSuccess: () => { closeModal(); show('Berhasil diperbarui!') },
    })
  } else {
    form.post(route('recurring.store'), {
      preserveScroll: true,
      onSuccess: () => { closeModal(); show('Recurring berhasil ditambahkan!') },
    })
  }
}

function toggleActive(item) {
  router.patch(route('recurring.toggle', item.id), {}, {
    preserveScroll: true,
    onSuccess: () => show(item.is_active ? 'Dinonaktifkan' : 'Diaktifkan'),
  })
}

function confirmDelete(id) {
  itemToDelete.value = id
  confirmingDeletion.value = true
}

function executeDelete() {
  if (itemToDelete.value) {
    router.delete(route('recurring.destroy', itemToDelete.value), {
      preserveScroll: true,
      onSuccess: () => {
        confirmingDeletion.value = false
        itemToDelete.value = null
        show('Berhasil dihapus!')
      },
    })
  }
}

function cancelDelete() {
  confirmingDeletion.value = false
  itemToDelete.value = null
}

function formatRp(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID')
}

function freqLabel(val) {
  const f = props.frequencies.find(x => x.value === val)
  return f?.label || val
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
  <Head title="Recurring" />
  <AppLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Recurring</h1>
        <p class="text-sm text-gray-400 mt-0.5">Pengeluaran berulang otomatis</p>
      </div>
      <button @click="openAddModal" class="flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
        <Plus class="w-3.5 h-3.5" /> Tambah
      </button>
    </div>

    <!-- Empty -->
    <div v-if="recurrings.length === 0" class="text-center py-16">
      <p class="text-sm text-gray-400">Belum ada pengeluaran berulang</p>
      <button @click="openAddModal" class="mt-3 text-sm font-semibold text-brand-600">+ Buat pertama</button>
    </div>

    <!-- List -->
    <div v-else class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
      <div
        v-for="item in recurrings"
        :key="item.id"
        :class="['flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3.5 border-b border-gray-100 dark:border-gray-800 last:border-0 group transition-opacity gap-2 sm:gap-3', !item.is_active && 'opacity-40']"
      >
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-lg shrink-0">
            {{ item.category?.icon }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate">
              {{ item.name || item.merchant_name || item.category?.name }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
              {{ item.name && item.merchant_name ? item.merchant_name + ' · ' : '' }}{{ item.category?.name }} · {{ freqLabel(item.frequency) }} · Berikutnya: {{ formatDate(item.next_run_date) }}
            </p>
            <p v-if="item.notes" class="text-[11px] text-gray-400 mt-1 italic border-l-2 border-gray-200 dark:border-gray-700 pl-1.5 truncate max-w-md">
              "{{ item.notes }}"
            </p>
          </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto mt-1 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-0 border-gray-50 dark:border-gray-800/50">
          <span class="text-[13px] font-semibold font-mono text-gray-900 dark:text-white">{{ formatRp(item.amount) }}</span>
          <div class="flex items-center gap-0.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
            <button @click="toggleActive(item)" class="p-2 sm:p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800" :title="item.is_active ? 'Nonaktifkan' : 'Aktifkan'">
              <ToggleRight v-if="item.is_active" class="w-4.5 h-4.5 sm:w-4 sm:h-4 text-brand-500" />
              <ToggleLeft v-else class="w-4.5 h-4.5 sm:w-4 sm:h-4 text-gray-400" />
            </button>
            <button @click="openEditModal(item)" class="p-2 sm:p-1.5 rounded-md text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10">
              <Edit2 class="w-4 h-4 sm:w-3.5 sm:h-3.5" />
            </button>
            <button @click="confirmDelete(item.id)" class="p-2 sm:p-1.5 rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">
              <Trash2 class="w-4 h-4 sm:w-3.5 sm:h-3.5" />
            </button>
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
            <!-- Handle bar (mobile) -->
            <div class="sm:hidden flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-300 dark:bg-gray-700 rounded-full"></div></div>

            <div class="px-5 py-4">
              <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ editingItem ? 'Edit' : 'Tambah' }} Recurring</h3>
                <button @click="closeModal" class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"><X class="w-4 h-4 text-gray-400" /></button>
              </div>

              <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                  <select v-model="form.category_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" required>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Nama Tagihan</label>
                  <input v-model="form.name" type="text" placeholder="Contoh: Langganan Netflix, Listrik, dll." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" required />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Merchant (opsional)</label>
                  <input v-model="form.merchant_name" type="text" placeholder="Netflix, PLN, dll." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Nominal</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input v-model="form.amount" type="number" min="1" max="9999999999999" class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 font-mono" required
                      @invalid="(e) => e.target.setCustomValidity(e.target.value === '' ? 'Nominal wajib diisi.' : (Number(e.target.value) < 1 ? 'Nominal minimal Rp 1.' : 'Nominal terlalu besar (maks. Rp 9.999.999.999.999).'))"
                      @input="(e) => e.target.setCustomValidity('')"
                    />
                  </div>
                  <p v-if="form.errors.amount" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ form.errors.amount }}
                  </p>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Frekuensi</label>
                  <select v-model="form.frequency" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" required>
                    <option v-for="f in frequencies" :key="f.value" :value="f.value">{{ f.label }}</option>
                  </select>
                </div>

                <div v-if="!editingItem">
                  <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-500">Tanggal Mulai</label>
                    <div class="flex items-center gap-3">
                      <button type="button" @click="form.start_date = ''" class="text-[11px] font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">Clear</button>
                      <button type="button" @click="form.start_date = new Date().toISOString().split('T')[0]" class="text-[11px] font-medium text-brand-600 dark:text-brand-400 hover:text-brand-700">Hari Ini</button>
                    </div>
                  </div>
                  <input v-model="form.start_date" type="date" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" required />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Berakhir (opsional)</label>
                  <input v-model="form.end_date" type="date" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Catatan (opsional)</label>
                  <textarea v-model="form.notes" rows="2" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 resize-y min-h-[64px]" placeholder="Opsional"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                  <button type="button" @click="closeModal" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                  <button type="submit" :disabled="form.processing" class="flex-1 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50 transition-colors">{{ editingItem ? 'Simpan' : 'Tambah' }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Modal -->
    <Teleport to="body">
      <Transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="confirmingDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50" @click="cancelDelete"></div>
          <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xl w-full max-w-xs p-5 text-center">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Hapus Recurring?</h3>
            <p class="text-sm text-gray-400 mb-5">Transaksi lama tidak akan terhapus.</p>
            <div class="flex gap-2">
              <button @click="cancelDelete" class="flex-1 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:bg-gray-800 rounded-lg">Batal</button>
              <button @click="executeDelete" class="flex-1 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg">Hapus</button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
