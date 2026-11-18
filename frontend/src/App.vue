<script setup lang="ts">
import Button from 'primevue/button'
import Message from 'primevue/message'
import Select from 'primevue/select'
import Skeleton from 'primevue/skeleton'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import { computed, onMounted, ref } from 'vue'
import BookingDialog from './components/BookingDialog.vue'
import CancelBookingDialog from './components/CancelBookingDialog.vue'
import SlotCard from './components/SlotCard.vue'
import { useSchedule } from './composables/useSchedule'
import type { Slot } from './types'

const toast = useToast()
const { timezone, days, selectedDay, slotsOfSelectedDay, isLoading, loadError, load } = useSchedule()

const slotToBook = ref<Slot | null>(null)
const slotToCancel = ref<Slot | null>(null)

const isFirstLoad = computed(() => isLoading.value && days.value.length === 0)

onMounted(load)

function onBooked() {
  slotToBook.value = null
  toast.add({ severity: 'success', summary: 'Слот забронирован', life: 4000 })
  load()
}

function onConflict(message: string) {
  slotToBook.value = null
  toast.add({ severity: 'warn', summary: 'Не получилось забронировать', detail: message, life: 7000 })
  load()
}

function onCancelled() {
  slotToCancel.value = null
  toast.add({ severity: 'success', summary: 'Бронь отменена', life: 4000 })
  load()
}
</script>

<template>
  <main class="page">
    <header class="page__header">
      <div>
        <h1>Переговорная</h1>
        <p class="page__hint">
          Ежедневно с 09:00 до 18:00<template v-if="timezone">, время по поясу {{ timezone }}</template>
        </p>
      </div>

      <div class="toolbar">
        <Select
          v-model="selectedDay"
          :options="days"
          option-label="label"
          option-value="value"
          placeholder="Дата"
          aria-label="Дата"
          class="toolbar__day"
          :disabled="days.length === 0"
        />
        <Button
          icon="pi pi-refresh"
          severity="secondary"
          variant="outlined"
          aria-label="Обновить расписание"
          :loading="isLoading"
          @click="load"
        />
      </div>
    </header>

    <Message v-if="loadError" severity="error" class="page__error">
      <div class="page__error-body">
        <span>Не удалось загрузить расписание. {{ loadError.message }}</span>
        <Button label="Повторить" size="small" severity="danger" :loading="isLoading" @click="load" />
      </div>
    </Message>

    <section class="slots" aria-label="Слоты">
      <template v-if="isFirstLoad">
        <Skeleton v-for="n in 9" :key="n" height="4.25rem" />
      </template>

      <SlotCard
        v-for="slot in slotsOfSelectedDay"
        v-else
        :key="slot.id"
        :slot="slot"
        :timezone="timezone"
        @book="slotToBook = slot"
        @cancel="slotToCancel = slot"
      />
    </section>

    <p v-if="!isLoading && !loadError && days.length === 0" class="page__hint">
      Слотов пока нет. Заполните расписание командой <code>php artisan db:seed</code>.
    </p>
  </main>

  <BookingDialog
    v-if="slotToBook"
    :slot="slotToBook"
    :timezone="timezone"
    @close="slotToBook = null"
    @booked="onBooked"
    @conflict="onConflict"
  />

  <CancelBookingDialog
    v-if="slotToCancel?.booking_id"
    :slot="slotToCancel"
    :booking-id="slotToCancel.booking_id"
    :timezone="timezone"
    @close="slotToCancel = null"
    @cancelled="onCancelled"
  />

  <Toast position="top-center" />
</template>

<style scoped>
.page {
  max-width: 60rem;
  margin: 0 auto;
  padding: 2rem 1rem 3rem;
}

.page__header {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.page__hint {
  margin-top: 0.25rem;
  color: var(--p-text-muted-color);
}

.toolbar {
  display: flex;
  gap: 0.5rem;
}

.toolbar__day {
  min-width: 13rem;
}

.page__error {
  margin-bottom: 1rem;
}

.page__error-body {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
}

.slots {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(17rem, 1fr));
  gap: 0.75rem;
}

@media (max-width: 40rem) {
  .toolbar {
    width: 100%;
  }

  .toolbar__day {
    flex: 1;
  }
}
</style>
