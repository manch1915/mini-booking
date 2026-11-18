import { computed, ref } from 'vue'
import { fetchSchedule } from '../api/bookings'
import { ApiError } from '../api/http'
import type { Slot } from '../types'
import { dayKey, formatDay } from '../utils/datetime'

export function useSchedule() {
  const slots = ref<Slot[]>([])
  const timezone = ref('')
  const selectedDay = ref<string | null>(null)
  const isLoading = ref(false)
  const loadError = ref<ApiError | null>(null)

  let latestLoad = 0

  // Загрузки могут накладываться: после брони список обновляется, даже если
  // ручное обновление ещё идёт. Применяется только ответ на последний запрос.
  async function load() {
    const currentLoad = ++latestLoad

    isLoading.value = true
    loadError.value = null

    try {
      const schedule = await fetchSchedule()

      if (currentLoad === latestLoad) {
        slots.value = schedule.slots
        timezone.value = schedule.timezone
        selectedDay.value ??= defaultDay()
      }
    } catch (caught) {
      if (!(caught instanceof ApiError)) {
        throw caught
      }

      if (currentLoad === latestLoad) {
        loadError.value = caught
      }
    } finally {
      if (currentLoad === latestLoad) {
        isLoading.value = false
      }
    }
  }

  const days = computed(() => {
    const labels = new Map<string, string>()

    for (const slot of slots.value) {
      if (!labels.has(slotDay(slot))) {
        labels.set(slotDay(slot), formatDay(slot.starts_at, timezone.value))
      }
    }

    return [...labels].map(([value, label]) => ({ value, label }))
  })

  const slotsOfSelectedDay = computed(() => slots.value.filter((slot) => slotDay(slot) === selectedDay.value))

  function slotDay(slot: Slot): string {
    return dayKey(slot.starts_at, timezone.value)
  }

  function defaultDay(): string | null {
    const today = dayKey(new Date(), timezone.value)

    return days.value.some((day) => day.value === today) ? today : (days.value[0]?.value ?? null)
  }

  return { timezone, days, selectedDay, slotsOfSelectedDay, isLoading, loadError, load }
}
