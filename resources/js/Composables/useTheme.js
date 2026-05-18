import { ref, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

export function useTheme() {
  const isDark = ref(false)

  onMounted(() => {
    const stored = localStorage.getItem('theme')
    if (stored === 'dark') {
      isDark.value = true
    } else if (stored === 'light') {
      isDark.value = false
    } else {
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    applyTheme()
  })

  function toggle() {
    isDark.value = !isDark.value
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    applyTheme()
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
    document.documentElement.classList.toggle('dark', isDark.value)
  }

  return { isDark, toggle, setMode }
}
