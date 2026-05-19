<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useTheme } from '@/Composables/useTheme'
import { useToast } from '@/Composables/useToast'
import {
  LayoutDashboard, ReceiptText, TrendingUp, Wallet, Repeat,
  Settings, Plus, Sun, Moon, Bell, Search, User, ChevronDown, X
} from 'lucide-vue-next'

const props = defineProps({
  title: { type: String, default: 'MonitorDuit' },
})

const { isDark, toggle } = useTheme()
const { toasts } = useToast()
const page = usePage()
const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)
const userMenuOpen = ref(false)
const unreadCount = computed(() => page.props.unreadNotifications || 0)

// Bottom nav (Instagram-style: 5 items max)
const bottomNavLinks = [
  { name: 'Home', route: 'dashboard', icon: LayoutDashboard },
  { name: 'Transaksi', route: 'transactions.index', icon: ReceiptText },
  { name: 'Budget', route: 'budgets.index', icon: Wallet },
  { name: 'Statistik', route: 'statistics', icon: TrendingUp },
  { name: 'Profil', route: 'profile.edit', icon: User },
]

// Desktop nav (full)
const desktopNavLinks = [
  { name: 'Dashboard', route: 'dashboard' },
  { name: 'Transaksi', route: 'transactions.index' },
  { name: 'Budget', route: 'budgets.index' },
  { name: 'Recurring', route: 'recurring.index' },
  { name: 'Statistik', route: 'statistics' },
  { name: 'Pengaturan', route: 'settings' },
]

function isActive(routeName) {
  return route().current(routeName)
}
</script>

<template>
  <div class="min-h-screen bg-[#fafafa] dark:bg-black text-gray-900 dark:text-gray-100 font-sans">

    <!-- Toast Notifications -->
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[200] space-y-2 w-full max-w-sm px-4">
      <TransitionGroup
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-[-12px] opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-[-12px] opacity-0"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'px-4 py-3 rounded-xl text-white text-sm font-medium text-center shadow-lg',
            toast.type === 'success' ? 'bg-gray-900 dark:bg-white dark:text-gray-900' : '',
            toast.type === 'error' ? 'bg-red-500' : '',
            toast.type === 'info' ? 'bg-blue-500' : '',
          ]"
        >
          {{ toast.message }}
        </div>
      </TransitionGroup>

      <div v-if="flash?.success" class="px-4 py-3 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium text-center shadow-lg">
        {{ flash.success }}
      </div>
    </div>

    <!-- Top Bar (Instagram-style) -->
    <header class="sticky top-0 z-40 bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800">
      <div class="max-w-[935px] mx-auto px-4">
        <div class="flex items-center justify-between h-[60px]">

          <!-- Logo -->
          <Link :href="route('dashboard')" class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-emerald-600 rounded-xl flex items-center justify-center">
              <span class="text-white font-bold text-xs">M</span>
            </div>
            <span class="font-bold text-xl text-gray-900 dark:text-white tracking-tight hidden sm:block" style="font-family: 'Plus Jakarta Sans', sans-serif;">MonitorDuit</span>
          </Link>

          <!-- Desktop Nav (center, Instagram-style) -->
          <nav class="hidden md:flex items-center gap-1">
            <Link
              v-for="link in desktopNavLinks"
              :key="link.route"
              :href="route(link.route)"
              :class="[
                'px-3 py-1.5 rounded-lg text-[13px] font-medium transition-colors',
                isActive(link.route)
                  ? 'text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800'
                  : 'text-gray-500 hover:text-gray-900 dark:hover:text-white',
              ]"
            >
              {{ link.name }}
            </Link>
          </nav>

          <!-- Right icons -->
          <div class="flex items-center gap-1">
            <!-- Add New (Instagram "+") -->
            <Link
              :href="route('transactions.create')"
              class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
              title="Tambah transaksi"
            >
              <Plus class="w-[22px] h-[22px]" />
            </Link>

            <!-- Notifications (Instagram heart) -->
            <Link
              :href="route('notifications.index')"
              class="relative p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
              title="Notifikasi"
            >
              <Bell class="w-[22px] h-[22px]" />
              <span
                v-if="unreadCount > 0"
                class="absolute top-1 right-1 min-w-[16px] h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"
              >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </Link>

            <!-- Theme toggle -->
            <button
              @click="toggle"
              class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors hidden sm:block"
            >
              <Moon v-if="isDark" class="w-[20px] h-[20px]" />
              <Sun v-else class="w-[20px] h-[20px]" />
            </button>

            <!-- User Avatar (Instagram profile pic) -->
            <div class="relative ml-1">
              <button
                @click="userMenuOpen = !userMenuOpen"
                :class="[
                  'w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold transition-all',
                  isActive('profile.edit')
                    ? 'ring-2 ring-gray-900 dark:ring-white ring-offset-2 ring-offset-white dark:ring-offset-gray-950'
                    : 'hover:opacity-80',
                ]"
                style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
              >
                {{ user?.name?.[0]?.toUpperCase() }}
              </button>

              <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-800 py-1 z-50">
                  <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ user?.name }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ user?.email }}</p>
                  </div>
                  <Link :href="route('profile.edit')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800" @click="userMenuOpen = false">
                    <User class="w-4 h-4" /> Profil
                  </Link>
                  <Link :href="route('settings')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800" @click="userMenuOpen = false">
                    <Settings class="w-4 h-4" /> Pengaturan
                  </Link>
                  <Link :href="route('recurring.index')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 md:hidden" @click="userMenuOpen = false">
                    <Repeat class="w-4 h-4" /> Recurring
                  </Link>
                  <button @click="toggle(); userMenuOpen = false" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 sm:hidden text-left">
                    <Moon v-if="isDark" class="w-4 h-4" />
                    <Sun v-else class="w-4 h-4" />
                    {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
                  </button>
                  <hr class="my-1 border-gray-100 dark:border-gray-800">
                  <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">
                    Keluar
                  </Link>
                </div>
              </Transition>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-[935px] mx-auto px-4 py-5 pb-24 md:pb-8 min-h-[calc(100vh-60px)]">
      <slot />
    </main>

    <!-- Footer (desktop only) -->
    <footer class="hidden md:block border-t border-gray-200 dark:border-gray-800">
      <div class="max-w-[935px] mx-auto px-4 py-6">
        <div class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs text-gray-400">
          <Link :href="route('about')" class="hover:text-gray-600 dark:hover:text-gray-300">Tentang</Link>
          <Link :href="route('help')" class="hover:text-gray-600 dark:hover:text-gray-300">Bantuan</Link>
          <Link :href="route('privacy')" class="hover:text-gray-600 dark:hover:text-gray-300">Privasi</Link>
          <Link :href="route('terms')" class="hover:text-gray-600 dark:hover:text-gray-300">Ketentuan</Link>
          <span>© {{ new Date().getFullYear() }} MonitorDuit</span>
        </div>
      </div>
    </footer>

    <!-- Bottom Navigation Bar (Instagram-style, mobile only) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 safe-area-bottom">
      <div class="flex items-center justify-around h-[50px] max-w-lg mx-auto">
        <Link
          v-for="link in bottomNavLinks"
          :key="link.route"
          :href="route(link.route)"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full transition-colors',
            isActive(link.route)
              ? 'text-gray-900 dark:text-white'
              : 'text-gray-400 dark:text-gray-500',
          ]"
        >
          <!-- Special avatar for Profile tab -->
          <template v-if="link.route === 'profile.edit'">
            <div
              :class="[
                'w-6 h-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold',
                isActive(link.route) ? 'ring-[1.5px] ring-gray-900 dark:ring-white ring-offset-1 ring-offset-white dark:ring-offset-gray-950' : '',
              ]"
              style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
            >
              {{ user?.name?.[0]?.toUpperCase() }}
            </div>
          </template>
          <template v-else>
            <component
              :is="link.icon"
              :class="[
                'transition-all',
                isActive(link.route) ? 'w-[26px] h-[26px]' : 'w-6 h-6',
              ]"
              :stroke-width="isActive(link.route) ? 2.5 : 1.5"
            />
          </template>
        </Link>
      </div>
    </nav>

    <!-- Click outside overlay -->
    <div v-if="userMenuOpen" class="fixed inset-0 z-30" @click="userMenuOpen = false"></div>
  </div>
</template>

<style scoped>
.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
</style>
