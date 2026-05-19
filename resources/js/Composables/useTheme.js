import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Global state so all components share the same reactivity
const isDark = ref(false)
let initialized = false

export function useTheme() {
  onMounted(() => {
    if (!initialized && typeof window !== 'undefined') {
      const stored = localStorage.getItem('theme')
      if (stored === 'dark') {
        isDark.value = true
      } else if (stored === 'light') {
        isDark.value = false
      } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
      }
      applyTheme()
      initialized = true
    }
  })

  function toggle() {
    isDark.value = !isDark.value
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    applyTheme()
    
    // Optionally sync with backend user preference
    router.put(route('settings.update'), {
      dark_mode_preference: isDark.value ? 'dark' : 'light'
    }, { preserveScroll: true, preserveState: true })
  }

  function setMode(mode) {
    if (mode === 'dark') {
      isDark.value = true
    } else if (mode === 'light') {
      isDark.value = false
    } else {
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    localStorage.setItem('theme', mode === 'system' ? '' : (isDark.value ? 'dark' : 'light'))
    applyTheme()
  }

  function applyTheme() {
    if (typeof document !== 'undefined') {
      document.documentElement.classList.toggle('dark', isDark.value)
    }
  }

  return { isDark, toggle, setMode }
}
