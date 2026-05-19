<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useTheme } from '@/Composables/useTheme'
import { useToast } from '@/Composables/useToast'
import axios from 'axios'
import { Sun, Moon, Monitor, Edit2, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  user: Object,
  supportedCurrencies: Array,
})

const { setMode } = useTheme()
const { show, error } = useToast()

const prefForm = useForm({
  dark_mode_preference: props.user.dark_mode_preference,
  timezone: props.user.timezone,
  default_currency: props.user.default_currency || 'IDR',
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

const notifForm = useForm({
  notif_budget_warning: props.user.notif_budget_warning ?? true,
  notif_budget_exceeded: props.user.notif_budget_exceeded ?? true,
  notif_reminder_email: props.user.notif_reminder_email ?? true,
  reminder_idle_days: props.user.reminder_idle_days ?? 3,
})

function saveNotifPreferences() {
  notifForm.put(route('settings.update'), {
    preserveScroll: true,
    onSuccess: () => show('Pengaturan notifikasi berhasil disimpan'),
  })
}

const categories = ref([])
const loadingCategories = ref(true)
const confirmingCatDeletion = ref(false)
const catToDelete = ref(null)

const newCatForm = useForm({ name: '', color: '#10b981', icon: '📦' })
const editCatForm = useForm({ name: '', color: '' })
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

function toggleEmojiPicker() { showEmojiPicker.value = !showEmojiPicker.value }
function selectEmoji(emoji) { newCatForm.icon = emoji; showEmojiPicker.value = false }

onMounted(() => loadCategories())

function submitNewCategory() {
  newCatForm.post(route('categories.store'), {
    preserveScroll: true,
    onSuccess: () => { newCatForm.reset(); loadCategories(); show('Kategori ditambahkan') }
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
    onSuccess: () => { editingCatId.value = null; loadCategories(); show('Kategori diperbarui') }
  })
}

function confirmDeleteCategory(id) { catToDelete.value = id; confirmingCatDeletion.value = true }

function executeDeleteCategory() {
  if (catToDelete.value) {
    router.delete(route('categories.destroy', catToDelete.value), {
      preserveScroll: true,
      onSuccess: () => { closeDeleteModal(); loadCategories(); show('Kategori dihapus') },
      onError: (errors) => { closeDeleteModal(); error(errors.category || 'Gagal menghapus kategori.') }
    })
  }
}

function closeDeleteModal() {
  confirmingCatDeletion.value = false
  setTimeout(() => { catToDelete.value = null }, 300)
}
</script>

<template>
  <Head title="Pengaturan" />
  <AppLayout>
    <div class="mb-5">
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Pengaturan</h1>
      <p class="text-sm text-gray-400 mt-0.5">Preferensi, notifikasi, dan kategori</p>
    </div>

    <div class="space-y-4">
      <!-- Preferensi -->
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Preferensi</h2>
        </div>
        <div class="p-5">
          <form @submit.prevent="savePreferences" class="space-y-5 max-w-md">
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-2">Tema Aplikasi</label>
              <div class="grid grid-cols-3 gap-2">
                <label :class="['cursor-pointer p-3 border rounded-lg text-center transition-colors', prefForm.dark_mode_preference === 'light' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800']">
                  <input type="radio" v-model="prefForm.dark_mode_preference" value="light" class="hidden" />
                  <Sun class="w-5 h-5 mx-auto mb-1.5" /><span class="text-xs font-medium">Terang</span>
                </label>
                <label :class="['cursor-pointer p-3 border rounded-lg text-center transition-colors', prefForm.dark_mode_preference === 'dark' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800']">
                  <input type="radio" v-model="prefForm.dark_mode_preference" value="dark" class="hidden" />
                  <Moon class="w-5 h-5 mx-auto mb-1.5" /><span class="text-xs font-medium">Gelap</span>
                </label>
                <label :class="['cursor-pointer p-3 border rounded-lg text-center transition-colors', prefForm.dark_mode_preference === 'system' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800']">
                  <input type="radio" v-model="prefForm.dark_mode_preference" value="system" class="hidden" />
                  <Monitor class="w-5 h-5 mx-auto mb-1.5" /><span class="text-xs font-medium">Sistem</span>
                </label>
              </div>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">Zona Waktu</label>
              <select v-model="prefForm.timezone" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500">
                <option value="Asia/Jakarta">WIB (Asia/Jakarta)</option>
                <option value="Asia/Makassar">WITA (Asia/Makassar)</option>
                <option value="Asia/Jayapura">WIT (Asia/Jayapura)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">Mata Uang Default</label>
              <select v-model="prefForm.default_currency" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                <option v-for="c in supportedCurrencies" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <button type="submit" :disabled="prefForm.processing" class="px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">Simpan</button>
          </form>
        </div>
      </div>

      <!-- Notifikasi -->
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h2>
        </div>
        <div class="p-5">
          <form @submit.prevent="saveNotifPreferences" class="space-y-3 max-w-md">
            <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <div><p class="text-sm font-medium text-gray-900 dark:text-white">Budget hampir habis</p><p class="text-xs text-gray-400 mt-0.5">Peringatan ≥80%</p></div>
              <input type="checkbox" v-model="notifForm.notif_budget_warning" class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
            </label>
            <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <div><p class="text-sm font-medium text-gray-900 dark:text-white">Budget terlampaui</p><p class="text-xs text-gray-400 mt-0.5">Peringatan melebihi batas</p></div>
              <input type="checkbox" v-model="notifForm.notif_budget_exceeded" class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
            </label>
            <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <div><p class="text-sm font-medium text-gray-900 dark:text-white">Reminder pencatatan</p><p class="text-xs text-gray-400 mt-0.5">Email pengingat</p></div>
              <input type="checkbox" v-model="notifForm.notif_reminder_email" class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
            </label>
            <div v-if="notifForm.notif_reminder_email" class="pl-4 border-l-2 border-brand-200 dark:border-brand-500/30">
              <label class="block text-xs text-gray-500 mb-1">Ingatkan setelah</label>
              <div class="flex items-center gap-2">
                <input v-model="notifForm.reminder_idle_days" type="number" min="1" max="30" class="w-16 px-3 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-mono text-center text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
                <span class="text-sm text-gray-400">hari</span>
              </div>
            </div>
            <button type="submit" :disabled="notifForm.processing" class="px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors mt-1">Simpan</button>
          </form>
        </div>
      </div>

      <!-- Kategori -->
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Kategori</h2>
        </div>
        <div class="p-5">
          <form @submit.prevent="submitNewCategory" class="flex items-end gap-2 mb-5 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="relative">
              <label class="block text-xs text-gray-400 mb-1">Icon</label>
              <button type="button" @click="toggleEmojiPicker" class="w-10 h-9 flex items-center justify-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-lg">{{ newCatForm.icon }}</button>
              <div v-if="showEmojiPicker" class="absolute z-10 mt-1 w-56 p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl grid grid-cols-6 gap-1">
                <button v-for="emoji in commonEmojis" :key="emoji" type="button" @click="selectEmoji(emoji)" class="w-8 h-8 flex items-center justify-center text-lg hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">{{ emoji }}</button>
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-400 mb-1">Nama</label>
              <input v-model="newCatForm.name" type="text" placeholder="Kopi" class="w-full px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" required />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Warna</label>
              <input v-model="newCatForm.color" type="color" class="h-9 w-10 rounded-lg cursor-pointer" required />
            </div>
            <button type="submit" :disabled="newCatForm.processing" class="px-3 py-1.5 h-9 bg-brand-600 text-white text-sm font-semibold rounded-lg">Tambah</button>
          </form>
          <p v-if="newCatForm.errors.name" class="text-red-500 text-xs mb-3">{{ newCatForm.errors.name }}</p>

          <div v-if="loadingCategories" class="text-center py-6 text-gray-400 text-sm">Loading...</div>
          <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
            <div v-for="cat in categories" :key="cat.id" class="flex items-center justify-between py-3 group">
              <template v-if="editingCatId === cat.id">
                <div class="flex items-center gap-2 flex-1 mr-3">
                  <span class="w-8 text-center text-lg">{{ cat.icon }}</span>
                  <input v-model="editCatForm.name" type="text" class="flex-1 px-2 py-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500" />
                  <input v-model="editCatForm.color" type="color" class="h-7 w-8 rounded cursor-pointer" />
                </div>
                <div class="flex gap-1.5">
                  <button @click="updateCategory(cat.id)" class="px-2.5 py-1 bg-brand-600 text-white text-xs font-medium rounded">Simpan</button>
                  <button @click="editingCatId = null" class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs rounded">Batal</button>
                </div>
              </template>
              <template v-else>
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-base">{{ cat.icon }}</div>
                  <div>
                    <p class="text-[13px] font-medium text-gray-900 dark:text-white">{{ cat.name }}</p>
                    <p class="text-xs text-gray-400 flex items-center gap-1.5 mt-0.5">
                      <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: cat.color }"></span>
                      {{ cat.is_default ? 'Default' : 'Custom' }} · {{ cat.transactions_count }} transaksi
                    </p>
                  </div>
                </div>
                <div class="flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                  <template v-if="!cat.is_default">
                    <button @click="startEditCategory(cat)" class="p-1.5 text-gray-400 hover:text-blue-500 rounded-md"><Edit2 class="w-3.5 h-3.5" /></button>
                    <button @click="confirmDeleteCategory(cat.id)" class="p-1.5 text-gray-400 hover:text-red-500 rounded-md"><Trash2 class="w-3.5 h-3.5" /></button>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <Teleport to="body">
      <Transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="confirmingCatDeletion" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/50" @click="closeDeleteModal"></div>
          <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xl w-full max-w-xs p-5 text-center">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Hapus kategori?</h3>
            <p class="text-sm text-gray-400 mb-5">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2">
              <button @click="closeDeleteModal" class="flex-1 py-2 text-sm font-semibold text-gray-600 bg-gray-100 dark:bg-gray-800 rounded-lg">Batal</button>
              <button @click="executeDeleteCategory" class="flex-1 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg">Hapus</button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
