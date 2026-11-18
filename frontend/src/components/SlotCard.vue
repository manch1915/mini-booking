<script setup lang="ts">
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import type { Slot, SlotStatus } from '../types'
import { formatTimeRange } from '../utils/datetime'

defineProps<{ slot: Slot; timezone: string }>()

defineEmits<{ book: []; cancel: [] }>()

const STATUS_TAGS: Record<SlotStatus, { label: string; severity: string }> = {
  free: { label: 'Свободно', severity: 'success' },
  booked: { label: 'Занято', severity: 'warn' },
  past: { label: 'Прошло', severity: 'secondary' },
}
</script>

<template>
  <article class="slot" :class="`slot--${slot.status}`">
    <div class="slot__info">
      <span class="slot__time">{{ formatTimeRange(slot, timezone) }}</span>
      <Tag :value="STATUS_TAGS[slot.status].label" :severity="STATUS_TAGS[slot.status].severity" />
    </div>

    <Button v-if="slot.status === 'free'" label="Забронировать" size="small" @click="$emit('book')" />
    <Button
      v-else-if="slot.status === 'booked'"
      label="Отменить бронь"
      size="small"
      severity="danger"
      variant="outlined"
      @click="$emit('cancel')"
    />
  </article>
</template>

<style scoped>
.slot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  min-height: 4.25rem;
  padding: 0.875rem 1rem;
  border: 1px solid var(--p-content-border-color);
  border-radius: var(--p-content-border-radius);
  background: var(--p-content-background);
}

.slot--past {
  background: transparent;
  color: var(--p-text-muted-color);
}

.slot__info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.375rem;
}

.slot__time {
  font-size: 1.0625rem;
  font-weight: 600;
  font-variant-numeric: tabular-nums;
}
</style>
