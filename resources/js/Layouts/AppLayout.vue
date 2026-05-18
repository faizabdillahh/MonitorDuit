<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { useTheme } from '@/Composables/useTheme'
import { useToast } from '@/Composables/useToast'
import {
  LayoutDashboard,
  ReceiptText,
  TrendingUp,
  Settings,
  ScanLine,
  Sun,
  Moon,
  Menu,
  ChevronDown
} from 'lucide-vue-next'

const props = defineProps({
  title: { type: String, default: 'MonitorDuit' },
})

const { isDark, toggle } = useTheme()
const { toasts } = useToast()
const page = usePage()
const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)

const navLinks = [
  { name: 'Dashboard', route: 'dashboard', icon: LayoutDashboard },
  { name: 'Transaksi', route: 'transactions.index', icon: ReceiptText },
  { name: 'Statistik', route: 'statistics', icon: TrendingUp },
  { name: 'Pengaturan', route: 'settings', icon: Settings },
]

function isActive(routeName) {
  return route().current(routeName)
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-200">
    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <TransitionGroup
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[280px]',
            toast.type === 'success' ? 'bg-brand-500' : '',
            toast.type === 'error' ? 'bg-red-500' : '',
            toast.type === 'info' ? 'bg-blue-500' : '',
          ]"
        >
          {{ toast.message }}
        </div>
      </TransitionGroup>

      <!-- Flash Messages -->
      <div v-if="flash?.success" class="px-4 py-3 rounded-xl shadow-lg bg-brand-500 text-white text-sm font-medium min-w-[280px]">
        {{ flash.success }}
      </div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo & Nav Links -->
          <div class="flex items-center gap-8">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
              <div class="w-9 h-9 bg-gradient-to-br from-brand-400 to-brand-600 rounded-xl flex items-center justify-center shadow-lg shadow-brand-500/25">
                <span class="text-white font-bold text-sm">M</span>
              </div>
              <span class="font-bold text-lg text-slate-900 dark:text-white hidden sm:block">MonitorDuit</span>
            </Link>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-1">
              <Link
                v-for="link in navLinks"
                :key="link.route"
                :href="route(link.route)"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                  isActive(link.route)
                    ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white',
                ]"
              >
                <component :is="link.icon" class="w-4 h-4 mr-1.5" />
                {{ link.name }}
              </Link>
            </div>
          </div>

          <!-- Right Side -->
          <div class="flex items-center gap-3">
            <!-- Scan CTA -->
            <Link
              :href="route('transactions.create')"
              class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-brand-500/25 transition-all duration-200 hover:shadow-brand-500/40 hover:-translate-y-0.5 active:translate-y-0"
            >
              <ScanLine class="w-4 h-4" />
              Scan Struk
            </Link>

            <!-- Dark Mode Toggle -->
            <button
              @click="toggle"
              class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
            >
              <Moon v-if="isDark" class="w-5 h-5" />
              <Sun v-else class="w-5 h-5" />
            </button>

            <!-- User Menu -->
            <div class="relative">
              <button
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-brand-400 to-teal-500 rounded-lg flex items-center justify-center">
                  <span class="text-white text-sm font-bold">{{ user?.name?.[0]?.toUpperCase() }}</span>
                </div>
                <span class="hidden md:block text-sm font-medium text-slate-700 dark:text-slate-300">{{ user?.name }}</span>
                <ChevronDown class="w-4 h-4 text-slate-400" />
              </button>

              <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50">
                  <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" @click="userMenuOpen = false">
                    Profil
                  </Link>
                  <Link :href="route('settings')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" @click="userMenuOpen = false">
                    Pengaturan
                  </Link>
                  <hr class="my-1 border-slate-200 dark:border-slate-700">
                  <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10">
                    Keluar
                  </Link>
                </div>
              </Transition>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"
            >
              <Menu class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Nav -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="-translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-2 opacity-0"
      >
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pb-3 pt-2 px-4 space-y-1">
          <Link
            v-for="link in navLinks"
            :key="link.route"
            :href="route(link.route)"
            :class="[
              'block px-3 py-2.5 rounded-lg text-sm font-medium',
              isActive(link.route)
                ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400'
                : 'text-slate-600 dark:text-slate-400',
            ]"
            @click="mobileMenuOpen = false"
          >
            <component :is="link.icon" class="w-5 h-5 mr-3" />
            {{ link.name }}
          </Link>
        </div>
      </Transition>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 min-h-[calc(100vh-16rem)]">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex justify-center md:justify-start items-center gap-2 mb-4 md:mb-0">
            <div class="w-6 h-6 bg-gradient-to-br from-brand-400 to-brand-600 rounded flex items-center justify-center shadow-sm">
              <span class="text-white font-bold text-[10px]">M</span>
            </div>
            <span class="text-slate-900 dark:text-white font-semibold text-sm">MonitorDuit</span>
          </div>
          
          <div class="flex justify-center gap-6 text-sm text-slate-500 dark:text-slate-400 flex-wrap">
            <Link :href="route('about')" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Tentang Kami</Link>
            <Link :href="route('help')" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Bantuan</Link>
            <Link :href="route('privacy')" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Privasi</Link>
            <Link :href="route('terms')" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Syarat & Ketentuan</Link>
          </div>
        </div>
        <div class="mt-8 border-t border-slate-100 dark:border-slate-800/50 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-400 dark:text-slate-500">
          <p>&copy; {{ new Date().getFullYear() }} MonitorDuit. Seluruh hak cipta dilindungi.</p>
          <p class="mt-2 md:mt-0 font-medium">Versi 1.1</p>
        </div>
      </div>
    </footer>

    <!-- Mobile FAB: Scan Struk -->
    <Link
      :href="route('transactions.create')"
      class="sm:hidden fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-brand-500 to-brand-600 text-white rounded-2xl shadow-xl shadow-brand-500/30 flex items-center justify-center z-50 hover:shadow-brand-500/50 active:scale-95 transition-all"
    >
      <ScanLine class="w-6 h-6" />
    </Link>

    <!-- Click outside to close menus -->
    <div v-if="userMenuOpen || mobileMenuOpen" class="fixed inset-0 z-30" @click="userMenuOpen = false; mobileMenuOpen = false"></div>
  </div>
</template>
