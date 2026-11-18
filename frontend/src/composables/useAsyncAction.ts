import { ref } from 'vue'
import { ApiError } from '../api/http'

/**
 * Оборачивает запрос: пока он идёт, повторный запуск игнорируется,
 * а ошибка API попадает в `error` вместо исключения.
 */
export function useAsyncAction<Args extends unknown[]>(action: (...args: Args) => Promise<unknown>) {
  const isPending = ref(false)
  const error = ref<ApiError | null>(null)

  async function run(...args: Args): Promise<boolean> {
    if (isPending.value) {
      return false
    }

    isPending.value = true
    error.value = null

    try {
      await action(...args)
      return true
    } catch (caught) {
      if (!(caught instanceof ApiError)) {
        throw caught
      }

      error.value = caught
      return false
    } finally {
      isPending.value = false
    }
  }

  return { isPending, error, run }
}
