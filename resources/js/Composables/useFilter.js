import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

export function useFilter(routeName, initialFilters = {}) {
  const filters = ref({ ...initialFilters })

  const applyFilters = debounce(() => {
    const params = {}
    for (const [key, value] of Object.entries(filters.value)) {
      if (value !== null && value !== '' && value !== undefined) {
        params[key] = value
      }
    }
    router.get(route(routeName), params, {
      preserveState: true,
      replace: true,
    })
  }, 300)

  function resetFilters() {
    for (const key in filters.value) {
      filters.value[key] = ''
    }
    applyFilters()
  }

  return { filters, applyFilters, resetFilters }
}
