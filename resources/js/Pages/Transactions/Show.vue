<script setup>
import { ref } from 'vue'
import { Link, Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ChevronLeft, Camera, ZoomIn, X } from 'lucide-vue-next'

const props = defineProps({
  transaction: Object,
  categories: Array,
  editing: { type: Boolean, default: false },
  receiptUrl: String,
})

const isEditing = ref(props.editing)
const showLightbox = ref(false)
const confirmingDeletion = ref(false)

const form = useForm({
  merchant_name: props.transaction.merchant_name || '',
  total_amount: props.transaction.total_amount,
  transaction_date: props.transaction.transaction_date,
  category_id: props.transaction.category_id,
  notes: props.transaction.notes || '',
})

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

function submit() {
  form.put(route('transactions.update', props.transaction.id), {
    onSuccess: () => {
      isEditing.value = false
    }
  })
}

function confirmDelete() {
  confirmingDeletion.value = true
}

function deleteTransaction() {
  form.delete(route('transactions.destroy', props.transaction.id), {
    onSuccess: () => closeModal(),
  })
}

function closeModal() {
  confirmingDeletion.value = false
}
</script>

<template>
  <Head :title="'Detail Transaksi'" />
  <AppLayout title="Detail Transaksi">
    
    <div class="mb-6 flex items-center justify-between">
      <Link :href="route('transactions.index')" class="text-slate-500 hover:text-brand-600 dark:text-slate-400 dark:hover:text-brand-400 font-medium flex items-center gap-1">
        <ChevronLeft class="w-4 h-4" />
        Kembali
      </Link>
      
      <div v-if="!isEditing" class="flex items-center gap-2">
        <button @click="isEditing = true" class="px-4 py-2 bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 font-semibold rounded-xl hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors">
          Edit
        </button>
        <button @click="confirmDelete" class="px-4 py-2 bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 font-semibold rounded-xl hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
          Hapus
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Info / Edit Form -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Detail Transaksi</h2>
        
        <form v-if="isEditing" @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Total Harga <span class="text-red-500">*</span></label>
            <input v-model="form.total_amount" type="number" step="0.01" min="1" max="9999999999999"
              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500"
              required
              @invalid="(e) => e.target.setCustomValidity(e.target.value === '' ? 'Total harga wajib diisi.' : (Number(e.target.value) < 1 ? 'Total harga minimal Rp 1.' : 'Nominal terlalu besar (maks. Rp 9.999.999.999.999).'))"
              @input="(e) => e.target.setCustomValidity('')"
            />
            <p v-if="form.errors.total_amount" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
              <svg class="w-3.5 h-3.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              {{ form.errors.total_amount }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Merchant</label>
            <input v-model="form.merchant_name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" />
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Transaksi <span class="text-red-500">*</span></label>
              <div class="flex items-center gap-3">
                <button type="button" @click="form.transaction_date = ''" class="text-xs font-medium text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Clear</button>
                <button type="button" @click="form.transaction_date = new Date().toISOString().split('T')[0]" class="text-xs font-medium text-brand-600 dark:text-brand-400 hover:text-brand-700">Hari Ini</button>
              </div>
            </div>
            <input v-model="form.transaction_date" type="date" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" required />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select v-model="form.category_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" required>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
            <textarea v-model="form.notes" rows="2" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500 resize-y min-h-[64px]" style="min-height: 64px;"></textarea>
          </div>

          <div class="pt-4 flex justify-end gap-3">
            <button type="button" @click="isEditing = false" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700">
              Batal
            </button>
            <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl font-bold text-white bg-brand-500 hover:bg-brand-600 disabled:opacity-50">
              Simpan Perubahan
            </button>
          </div>
        </form>

        <div v-else class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pengeluaran</p>
              <p class="text-3xl font-bold font-mono text-slate-900 dark:text-white mt-1">
                <span v-if="transaction.currency !== 'IDR'" class="text-xl text-slate-500 font-sans font-normal mr-1">{{ transaction.currency }}</span>
                {{ transaction.currency !== 'IDR' ? Number(transaction.total_amount).toLocaleString('id-ID') : formatCurrency(transaction.total_amount) }}
              </p>
              <div v-if="transaction.currency !== 'IDR'" class="mt-1 flex items-center gap-2">
                <span class="text-sm font-semibold font-mono text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-500/10 px-2 py-0.5 rounded">
                  ≈ {{ formatCurrency(transaction.amount_idr) }}
                </span>
                <span class="text-[10px] text-slate-400">
                  (Rate: {{ Number(transaction.exchange_rate).toLocaleString('id-ID') }})
                </span>
              </div>
            </div>
            <div class="text-right">
              <span v-if="transaction.source === 'ai'" class="px-2 py-1 text-xs font-bold rounded-md bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-400">Diisi AI</span>
              <span v-else class="px-2 py-1 text-xs font-bold rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Manual</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-y-6">
            <div>
              <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Merchant</p>
              <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ transaction.merchant_name || '-' }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kategori</p>
              <div class="flex items-center gap-1.5 mt-1">
                <span>{{ transaction.category?.icon }}</span>
                <span class="text-base font-semibold text-slate-900 dark:text-white">{{ transaction.category?.name }}</span>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal</p>
              <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ new Date(transaction.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Dicatat Pada</p>
              <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ new Date(transaction.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
            </div>
          </div>

          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Catatan</p>
            <p class="text-base text-slate-900 dark:text-white mt-1 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">{{ transaction.notes || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Receipt Preview -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Bukti Struk</h2>
        
        <div v-if="receiptUrl" class="w-full">
          <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 group cursor-pointer" @click="showLightbox = true">
            <img :src="receiptUrl" class="w-full object-contain max-h-[500px]" alt="Foto struk" />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
              <span class="flex items-center gap-2 opacity-0 group-hover:opacity-100 text-white font-medium bg-black/50 px-3 py-1.5 rounded-lg backdrop-blur-sm transition-opacity">
                <ZoomIn class="w-4 h-4" /> Perbesar
              </span>
            </div>
          </div>
        </div>
        
        <div v-else class="h-[300px] flex flex-col items-center justify-center text-center bg-slate-50 dark:bg-slate-800 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
          <Camera class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" />
          <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada foto struk</p>
          <p class="text-xs text-slate-400 mt-1">Transaksi ini dicatat secara manual</p>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <div v-if="showLightbox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4" @click="showLightbox = false">
        <button class="absolute top-4 right-4 text-white hover:text-brand-400 p-2" @click="showLightbox = false">
          <X class="w-8 h-8" />
        </button>
        <img :src="receiptUrl" class="max-w-full max-h-[90vh] object-contain rounded-lg" @click.stop />
        <a :href="receiptUrl" download class="absolute bottom-6 right-6 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl font-medium shadow-lg" @click.stop>
          Download
        </a>
      </div>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="confirmingDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
          <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
          
          <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 transform transition-all">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
              Hapus Transaksi?
            </h3>
            <p class="text-slate-600 dark:text-slate-400">
              Tindakan ini tidak dapat dibatalkan. Transaksi ini akan dihapus secara permanen dari sistem.
            </p>
            <div class="mt-8 flex justify-end gap-3">
              <button @click="closeModal" class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Batal
              </button>
              <button @click="deleteTransaction" :disabled="form.processing" class="px-5 py-2.5 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/25 disabled:opacity-50">
                Ya, Hapus
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
