<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTheme } from '@/Composables/useTheme'
import { useToast } from '@/Composables/useToast'
import axios from 'axios'
import { Sun, Moon, Monitor, Edit2, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  user: Object,
})

const { setMode } = useTheme()
const { show, error } = useToast()

const prefForm = useForm({
  dark_mode_preference: props.user.dark_mode_preference,
  timezone: props.user.timezone,
})

function savePreferences() {
  prefForm.put(route('settings.update'), {
    preserveScroll: true,
    onSuccess: () => {
      setMode(prefForm.dark_mode_preference)
      show('Pengaturan berhasil disimpan')
    }
  })
}


// Category Management State
const categories = ref([])
const loadingCategories = ref(true)
const confirmingCatDeletion = ref(false)
const catToDelete = ref(null)

const newCatForm = useForm({
  name: '',
  color: '#10b981',
  icon: '📦',
})

const editCatForm = useForm({
  name: '',
  color: '',
})
const editingCatId = ref(null)

async function loadCategories() {
  loadingCategories.value = true
  try {
    const res = await axios.get(route('categories.index'))
    categories.value = res.data
  } finally {
    loadingCategories.value = false
  }
}

const showEmojiPicker = ref(false)
const commonEmojis = ['📦','🍔','🚗','🛍️','💡','🎬','🏥','🛒','👥','⚙️','📈','🏢','📄','💸','🏦','🎓','🎮','✈️','🍽️','👕','📱','🔧','🎁','💊']

function toggleEmojiPicker() {
  showEmojiPicker.value = !showEmojiPicker.value
}
function selectEmoji(emoji) {
  newCatForm.icon = emoji
  showEmojiPicker.value = false
}
onMounted(() => {
  loadCategories()
})

function submitNewCategory() {
  newCatForm.post(route('categories.store'), {
    preserveScroll: true,
    onSuccess: () => {
      newCatForm.reset()
      loadCategories()
      show('Kategori ditambahkan')
    }
  })
}

function startEditCategory(cat) {
  editingCatId.value = cat.id
  editCatForm.name = cat.name
  editCatForm.color = cat.color
}

function updateCategory(id) {
  editCatForm.put(route('categories.update', id), {
    preserveScroll: true,
    onSuccess: () => {
      editingCatId.value = null
      loadCategories()
      show('Kategori diperbarui')
    }
  })
}

function confirmDeleteCategory(id) {
  catToDelete.value = id
  confirmingCatDeletion.value = true
}

function executeDeleteCategory() {
  if (catToDelete.value) {
    router.delete(route('categories.destroy', catToDelete.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeDeleteModal()
        loadCategories()
        show('Kategori dihapus')
      },
      onError: (errors) => {
        closeDeleteModal()
        if (errors.category) {
          error(errors.category)
        } else {
          error('Terjadi kesalahan saat menghapus kategori.')
        }
      }
    })
  }
}

function closeDeleteModal() {
  confirmingCatDeletion.value = false
  setTimeout(() => {
    catToDelete.value = null
  }, 300)
}
</script>

<template>
  <Head title="Pengaturan" />
  <AppLayout title="Pengaturan">
    <div class="max-w-4xl mx-auto space-y-6">
      
      <!-- Tampilan & Preferensi -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Preferensi</h2>
        
        <form @submit.prevent="savePreferences" class="space-y-6 max-w-lg">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tema Aplikasi</label>
            <div class="grid grid-cols-3 gap-3">
              <label :class="['cursor-pointer p-3 border rounded-xl text-center transition-all', prefForm.dark_mode_preference === 'light' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400']">
                <input type="radio" v-model="prefForm.dark_mode_preference" value="light" class="hidden" />
                <Sun class="w-6 h-6 mx-auto mb-2" />
                <span class="text-xs font-semibold">Terang</span>
              </label>
              <label :class="['cursor-pointer p-3 border rounded-xl text-center transition-all', prefForm.dark_mode_preference === 'dark' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400']">
                <input type="radio" v-model="prefForm.dark_mode_preference" value="dark" class="hidden" />
                <Moon class="w-6 h-6 mx-auto mb-2" />
                <span class="text-xs font-semibold">Gelap</span>
              </label>
              <label :class="['cursor-pointer p-3 border rounded-xl text-center transition-all', prefForm.dark_mode_preference === 'system' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400']">
                <input type="radio" v-model="prefForm.dark_mode_preference" value="system" class="hidden" />
                <Monitor class="w-6 h-6 mx-auto mb-2" />
                <span class="text-xs font-semibold">Sistem</span>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Zona Waktu</label>
            <select v-model="prefForm.timezone" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
              <option value="Asia/Jakarta">WIB (Asia/Jakarta)</option>
              <option value="Asia/Makassar">WITA (Asia/Makassar)</option>
              <option value="Asia/Jayapura">WIT (Asia/Jayapura)</option>
            </select>
          </div>

          <button type="submit" :disabled="prefForm.processing" class="px-5 py-2.5 bg-brand-500 text-white font-medium rounded-xl hover:bg-brand-600 transition-colors">
            Simpan Preferensi
          </button>
        </form>
      </div>

      <!-- Kategori -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
          <h2 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Kategori</h2>
        </div>

        <!-- Tambah -->
        <form @submit.prevent="submitNewCategory" class="flex items-end gap-3 mb-6 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
          <div class="relative">
            <label class="block text-xs font-medium text-slate-500 mb-1">Icon</label>
            <button type="button" @click="toggleEmojiPicker" class="w-12 h-[38px] flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xl outline-none focus:ring-2 focus:ring-brand-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
              {{ newCatForm.icon }}
            </button>
            
            <div v-if="showEmojiPicker" class="absolute z-10 mt-1 w-64 p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl grid grid-cols-6 gap-1">
              <button v-for="emoji in commonEmojis" :key="emoji" type="button" @click="selectEmoji(emoji)" class="w-8 h-8 flex items-center justify-center text-xl hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                {{ emoji }}
              </button>
            </div>
          </div>
          <div class="flex-1">
            <label class="block text-xs font-medium text-slate-500 mb-1">Nama Kategori</label>
            <input v-model="newCatForm.name" type="text" placeholder="Misal: Kopi" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" required />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Warna</label>
            <input v-model="newCatForm.color" type="color" class="h-[38px] w-12 rounded-lg cursor-pointer" required />
          </div>
          <button type="submit" :disabled="newCatForm.processing" class="px-4 py-2 h-[38px] bg-brand-500 text-white font-medium text-sm rounded-lg hover:bg-brand-600 transition-colors">
            Tambah
          </button>
        </form>

        <p v-if="newCatForm.errors.name" class="text-red-500 text-sm mb-4">{{ newCatForm.errors.name }}</p>

        <!-- List -->
        <div v-if="loadingCategories" class="text-center py-8 text-slate-400">Loading...</div>
        <div v-else class="space-y-2">
          <div v-for="cat in categories" :key="cat.id" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 group transition-colors">
            
            <template v-if="editingCatId === cat.id">
              <div class="flex items-center gap-2 flex-1 mr-4">
                <span class="w-10 text-center text-lg">{{ cat.icon }}</span>
                <input v-model="editCatForm.name" type="text" class="flex-1 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500" />
                <input v-model="editCatForm.color" type="color" class="h-8 w-10 rounded-md cursor-pointer" />
              </div>
              <div class="flex items-center gap-2">
                <button @click="updateCategory(cat.id)" class="px-3 py-1.5 bg-brand-500 text-white text-xs font-medium rounded-md">Simpan</button>
                <button @click="editingCatId = null" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-md">Batal</button>
              </div>
            </template>

            <template v-else>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl" :style="{ backgroundColor: cat.color + '20' }">
                  {{ cat.icon }}
                </div>
                <div>
                  <p class="font-medium text-slate-900 dark:text-white text-sm">{{ cat.name }}</p>
                  <p class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                    <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: cat.color }"></span>
                    {{ cat.is_default ? 'Default' : 'Custom' }}
                    • <span class="font-mono">{{ cat.transactions_count }}</span> transaksi
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <template v-if="!cat.is_default">
                  <button @click="startEditCategory(cat)" class="p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-md"><Edit2 class="w-4 h-4" /></button>
                  <button @click="confirmDeleteCategory(cat.id)" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-md"><Trash2 class="w-4 h-4" /></button>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

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
        <div v-if="confirmingCatDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
          <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeDeleteModal"></div>
          
          <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 transform transition-all">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
              Hapus Kategori?
            </h3>
            <p class="text-slate-600 dark:text-slate-400">
              Kategori yang dihapus tidak dapat dikembalikan. Pastikan tidak ada transaksi penting yang terkait dengan kategori ini.
            </p>
            <div class="mt-8 flex justify-end gap-3">
              <button @click="closeDeleteModal" class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Batal
              </button>
              <button @click="executeDeleteCategory" class="px-5 py-2.5 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors shadow-lg shadow-red-500/25">
                Ya, Hapus
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
