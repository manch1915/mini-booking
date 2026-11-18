<script setup lang="ts">
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import { computed, reactive, ref } from 'vue'
import { createBooking } from '../api/bookings'
import { useAsyncAction } from '../composables/useAsyncAction'
import type { Customer, Slot } from '../types'
import { formatSlot } from '../utils/datetime'

type FieldErrors = Partial<Record<keyof Customer | 'slot_id', string>>

const EMAIL_PATTERN = /^\S+@\S+\.\S+$/

const props = defineProps<{ slot: Slot; timezone: string }>()

const emit = defineEmits<{
  close: []
  booked: []
  conflict: [message: string]
}>()

const customer = reactive<Customer>({ customer_name: '', customer_email: '' })
const fieldErrors = ref<FieldErrors>({})

const { isPending, error, run: book } = useAsyncAction(() => createBooking(props.slot.id, customer))

const requestError = computed(() => (error.value && !error.value.isValidation ? error.value.message : null))

async function submit() {
  fieldErrors.value = validate()

  if (Object.keys(fieldErrors.value).length > 0) {
    return
  }

  if (await book()) {
    emit('booked')
  } else if (error.value?.isConflict) {
    emit('conflict', error.value.message)
  } else if (error.value?.isValidation) {
    showServerErrors(firstMessages(error.value.fieldErrors))
  }
}

function showServerErrors(errors: FieldErrors) {
  // Ошибка в slot_id значит, что слота больше нет: форму исправлять бесполезно.
  if (errors.slot_id) {
    emit('conflict', errors.slot_id)
  } else {
    fieldErrors.value = errors
  }
}

function validate(): FieldErrors {
  const errors: FieldErrors = {}

  if (!customer.customer_name.trim()) {
    errors.customer_name = 'Укажите имя.'
  }

  if (!customer.customer_email.trim()) {
    errors.customer_email = 'Укажите email.'
  } else if (!EMAIL_PATTERN.test(customer.customer_email.trim())) {
    errors.customer_email = 'Проверьте формат email.'
  }

  return errors
}

function firstMessages(errors: Record<string, string[]>): FieldErrors {
  return Object.fromEntries(Object.entries(errors).map(([field, messages]) => [field, messages[0]]))
}
</script>

<template>
  <Dialog
    visible
    modal
    header="Новая бронь"
    :closable="!isPending"
    :draggable="false"
    class="dialog"
    @update:visible="emit('close')"
  >
    <p class="dialog__slot">{{ formatSlot(slot, timezone) }}</p>

    <form class="form" novalidate @submit.prevent="submit">
      <div class="field">
        <label for="customer-name">Имя</label>
        <InputText
          id="customer-name"
          v-model="customer.customer_name"
          autocomplete="name"
          autofocus
          fluid
          :invalid="!!fieldErrors.customer_name"
          :disabled="isPending"
        />
        <small v-if="fieldErrors.customer_name" class="field__error">{{ fieldErrors.customer_name }}</small>
      </div>

      <div class="field">
        <label for="customer-email">Email</label>
        <InputText
          id="customer-email"
          v-model="customer.customer_email"
          type="email"
          autocomplete="email"
          fluid
          :invalid="!!fieldErrors.customer_email"
          :disabled="isPending"
        />
        <small v-if="fieldErrors.customer_email" class="field__error">{{ fieldErrors.customer_email }}</small>
      </div>

      <Message v-if="requestError" severity="error">{{ requestError }}</Message>

      <div class="dialog__actions">
        <Button label="Закрыть" severity="secondary" variant="text" :disabled="isPending" @click="emit('close')" />
        <Button type="submit" :label="requestError ? 'Повторить' : 'Забронировать'" :loading="isPending" />
      </div>
    </form>
  </Dialog>
</template>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.field__error {
  color: var(--p-red-500);
}
</style>
