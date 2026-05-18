<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useToast } from '@/Composables/useToast'
import axios from 'axios'

const props = defineProps({
  categories: Array,
})

const { show, error } = useToast()

const form = useForm({
  merchant_name: '',
  total_amount: '',
  transaction_date: new Date().toISOString().split('T')[0],
  category_id: '',
  notes: '',
  source: 'manual',
  ai_confidence: null,
  ai_raw_response: null,
  receipt_image: null,
  receipt_image_path: null,
})

const previewUrl = ref(null)
const isScanning = ref(false)
const scanStatus = ref('')
const fileInput = ref(null)

function handleFileChange(e) {
  const file = e.target.files[0]
  if (!file) return

  if (file.size > 10 * 1024 * 1024) {
    error('Ukuran file maksimal 10MB')
    return
  }

  form.receipt_image = file
  previewUrl.value = URL.createObjectURL(file)
}

function triggerFileInput() {
  fileInput.value.click()
}

async function processScan() {
  if (!form.receipt_image) return

  isScanning.value = true
  scanStatus.value = 'Membaca struk kamu...'

  const formData = new FormData()
  formData.append('receipt_image', form.receipt_image)

  try {
    const response = await axios.post(route('receipts.scan'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    const data = response.data
    
    if (data.success) {
      show('Struk berhasil diproses!', 'success')
      form.source = 'ai'
      form.ai_confidence = data.confidence
      form.merchant_name = data.merchant_name || ''
      form.total_amount = data.total_amount || ''
      if (data.transaction_date) {
        form.transaction_date = data.transaction_date
      }
      form.receipt_image_path = data.image_path
    } else {
      if (data.error === 'not_a_receipt') {
        error('Sepertinya ini bukan struk. Silakan isi manual.')
      } else if (data.error === 'timeout') {
        error('Scan memakan waktu lama. Silakan isi manual.')
      } else {
        error('Gagal membaca struk. Silakan isi manual.')
      }
      form.source = 'manual'
    }
  } catch (e) {
    error(e.response?.data?.message || 'Terjadi kesalahan saat menghubungi server.')
    form.source = 'manual'
  } finally {
    isScanning.value = false
  }
}

function submit() {
  form.post(route('transactions.store'))
}
</script>

<template>
  <Head title="Tambah Transaksi" />
  <AppLayout title="Tambah Transaksi">
    <div class="max-w-3xl mx-auto">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Pengeluaran</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Catat transaksi baru dengan scan struk atau manual</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Scan Receipt Column -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col items-center justify-center min-h-[300px]">
          <input type="file" ref="fileInput" class="hidden" accept="image/jpeg,image/png,image/webp,image/heic" @change="handleFileChange" />
          
          <div v-if="!previewUrl" class="text-center w-full">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-100 dark:border-emerald-500/20">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-slate-700 dark:text-slate-300 font-semibold mb-2">Upload Foto Struk</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Format JPG, PNG, WEBP max 10MB</p>
            <button @click="triggerFileInput" type="button" class="px-5 py-2.5 bg-emerald-500 text-white font-medium rounded-xl hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/20">
              Pilih File Gambar
            </button>
          </div>

          <div v-else class="w-full flex flex-col items-center">
            <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 mb-4 bg-slate-100 dark:bg-slate-800">
              <img :src="previewUrl" class="w-full object-contain max-h-[300px]" alt="Preview struk" />
              <button v-if="!isScanning" @click="triggerFileInput" type="button" class="absolute top-2 right-2 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm p-2 rounded-lg shadow text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </button>
            </div>

            <button v-if="!isScanning && form.source === 'manual'" @click="processScan" type="button" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2">
              ✨ Ekstrak Data dengan AI
            </button>

            <div v-if="isScanning" class="w-full py-3 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-medium rounded-xl flex justify-center items-center gap-3">
              <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ scanStatus }}
            </div>

            <div v-if="form.source === 'ai'" class="w-full py-2.5 mt-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-medium rounded-xl text-center text-sm border border-green-200 dark:border-green-800">
              ✅ Data berhasil diekstrak
            </div>
          </div>
        </div>

        <!-- Form Column -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
          <form @submit.prevent="submit" class="space-y-4">
            
            <!-- AI Badge -->
            <div v-if="form.source === 'ai'" class="flex items-center gap-2 mb-2">
              <span class="px-2 py-1 text-xs font-bold rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                ✨ Diisi oleh AI
              </span>
              <span v-if="form.ai_confidence" :class="[
                'px-2 py-1 text-xs font-bold rounded-md',
                form.ai_confidence === 'high' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 
                form.ai_confidence === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400' : 
                'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'
              ]">
                Confidence: {{ form.ai_confidence.toUpperCase() }}
              </span>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Total Harga <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                <input v-model="form.total_amount" type="number" step="0.01" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" placeholder="0" required :class="{'ring-2 ring-emerald-500': form.source === 'ai' && form.total_amount}" />
              </div>
              <p v-if="form.errors.total_amount" class="text-red-500 text-xs mt-1">{{ form.errors.total_amount }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Merchant</label>
              <input v-model="form.merchant_name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: Indomaret, Tokopedia" :class="{'ring-2 ring-emerald-500': form.source === 'ai' && form.merchant_name}" />
              <p v-if="form.errors.merchant_name" class="text-red-500 text-xs mt-1">{{ form.errors.merchant_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
              <input v-model="form.transaction_date" type="date" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" required :class="{'ring-2 ring-emerald-500': form.source === 'ai' && form.transaction_date}" />
              <p v-if="form.errors.transaction_date" class="text-red-500 text-xs mt-1">{{ form.errors.transaction_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-red-500">*</span></label>
              <select v-model="form.category_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" required>
                <option value="" disabled>Pilih Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
              </select>
              <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
              <textarea v-model="form.notes" rows="2" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Opsional"></textarea>
              <p v-if="form.errors.notes" class="text-red-500 text-xs mt-1">{{ form.errors.notes }}</p>
            </div>

            <div class="pt-4 flex justify-end gap-3">
              <Link :href="route('transactions.index')" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Batal
              </Link>
              <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                Simpan Transaksi
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
