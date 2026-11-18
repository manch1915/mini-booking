<script setup lang="ts">
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Message from 'primevue/message'
import { cancelBooking } from '../api/bookings'
import { useAsyncAction } from '../composables/useAsyncAction'
import type { Slot } from '../types'
import { formatSlot } from '../utils/datetime'

const props = defineProps<{ slot: Slot; bookingId: number; timezone: string }>()

const emit = defineEmits<{ close: []; cancelled: [] }>()

const { isPending, error, run: cancel } = useAsyncAction(() => cancelBooking(props.bookingId))

async function confirm() {
  // 404 значит, что бронь уже отменили: для пользователя это тот же результат.
  if ((await cancel()) || error.value?.isNotFound) {
    emit('cancelled')
  }
}
</script>

<template>
  <Dialog
    visible
    modal
    header="Отменить бронь?"
    :closable="!isPending"
    :draggable="false"
    class="dialog"
    @update:visible="emit('close')"
  >
    <p class="dialog__slot">{{ formatSlot(slot, timezone) }}</p>
    <p>После отмены слот снова станет свободным.</p>

    <Message v-if="error" severity="error">{{ error.message }}</Message>

    <div class="dialog__actions">
      <Button label="Не отменять" severity="secondary" variant="text" :disabled="isPending" @click="emit('close')" />
      <Button
        :label="error ? 'Повторить' : 'Отменить бронь'"
        severity="danger"
        :loading="isPending"
        @click="confirm"
      />
    </div>
  </Dialog>
</template>
