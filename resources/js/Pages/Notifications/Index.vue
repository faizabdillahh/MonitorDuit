<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useToast } from '@/Composables/useToast'
import { Bell, CheckCheck, Trash2, Clock } from 'lucide-vue-next'

const props = defineProps({
  notifications: Object,
  unreadCount: Number,
})

const { show } = useToast()

function notifIcon(type) {
  const icons = { budget_warning: '⚠️', budget_exceeded: '🔴', recording_reminder: '📝' }
  return icons[type] || '🔔'
}

function markRead(id) {
  router.patch(route('notifications.read', id), {}, { preserveScroll: true })
}

function markAllRead() {
  router.patch(route('notifications.read-all'), {}, {
    preserveScroll: true,
    onSuccess: () => show('Semua ditandai dibaca'),
  })
}

function deleteNotif(id) {
  router.delete(route('notifications.destroy', id), { preserveScroll: true })
}

function navigateTo(notif) {
  if (!notif.read_at) markRead(notif.id)
  const actionUrl = notif.data?.action_url
  if (actionUrl) router.visit(actionUrl)
}

function formatTime(date) {
  const d = new Date(date)
  const now = new Date()
  const diff = Math.floor((now - d) / 1000)
  if (diff < 60) return 'Baru saja'
  if (diff < 3600) return Math.floor(diff / 60) + 'm'
  if (diff < 86400) return Math.floor(diff / 3600) + 'j'
  if (diff < 604800) return Math.floor(diff / 86400) + 'h'
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}
</script>

<template>
  <Head title="Notifikasi" />
  <AppLayout>
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Notifikasi</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ unreadCount > 0 ? `${unreadCount} belum dibaca` : 'Semua sudah dibaca' }}</p>
      </div>
      <button
        v-if="unreadCount > 0"
        @click="markAllRead"
        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-600 bg-brand-50 dark:bg-brand-500/10 rounded-lg hover:bg-brand-100 dark:hover:bg-brand-500/20 transition-colors"
      >
        <CheckCheck class="w-3.5 h-3.5" /> Tandai semua
      </button>
    </div>

    <!-- Notification List -->
    <div v-if="notifications.data.length > 0" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
      <div
        v-for="notif in notifications.data"
        :key="notif.id"
        @click="navigateTo(notif)"
        :class="[
          'flex items-center gap-3 px-4 py-3.5 border-b border-gray-100 dark:border-gray-800 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group',
          !notif.read_at && 'bg-blue-50/50 dark:bg-blue-500/5'
        ]"
      >
        <div class="w-11 h-11 rounded-full flex items-center justify-center text-lg shrink-0" :class="notif.read_at ? 'bg-gray-100 dark:bg-gray-800' : 'bg-brand-100 dark:bg-brand-500/20'">
          {{ notifIcon(notif.data?.type) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[13px] text-gray-900 dark:text-white">
            <span class="font-semibold">{{ notif.data?.title || 'Notifikasi' }}</span>
            <span class="text-gray-500 dark:text-gray-400 ml-1">{{ notif.data?.message }}</span>
          </p>
          <p class="text-xs text-gray-400 mt-0.5">{{ formatTime(notif.created_at) }}</p>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
          <div v-if="!notif.read_at" class="w-2 h-2 bg-brand-500 rounded-full"></div>
          <button @click.stop="deleteNotif(notif.id)" class="p-1.5 text-gray-300 hover:text-red-500 rounded-md opacity-0 group-hover:opacity-100 transition-all">
            <Trash2 class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else class="text-center py-16">
      <Bell class="w-8 h-8 text-gray-300 mx-auto mb-3" />
      <p class="text-sm text-gray-400">Belum ada notifikasi</p>
    </div>

    <!-- Pagination -->
    <div v-if="notifications.last_page > 1" class="flex justify-center gap-1 mt-5">
      <button
        v-for="link in notifications.links"
        :key="link.label"
        @click="link.url && router.get(link.url)"
        :disabled="!link.url"
        :class="[
          'px-3 py-1.5 rounded-lg text-xs font-medium',
          link.active ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' : link.url ? 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' : 'text-gray-300 cursor-not-allowed'
        ]"
        v-html="link.label"
      ></button>
    </div>
  </AppLayout>
</template>
