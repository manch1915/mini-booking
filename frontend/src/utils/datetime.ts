import type { Slot } from '../types'

type DateInput = string | Date

/** День в часовом поясе расписания в виде `2026-11-18`. */
export function dayKey(date: DateInput, timeZone: string): string {
  return new Intl.DateTimeFormat('en-CA', { timeZone, year: 'numeric', month: '2-digit', day: '2-digit' }).format(
    new Date(date),
  )
}

export function formatDay(date: DateInput, timeZone: string): string {
  return new Intl.DateTimeFormat('ru-RU', { timeZone, weekday: 'short', day: 'numeric', month: 'long' }).format(
    new Date(date),
  )
}

export function formatTime(date: DateInput, timeZone: string): string {
  return new Intl.DateTimeFormat('ru-RU', { timeZone, hour: '2-digit', minute: '2-digit' }).format(new Date(date))
}

export function formatTimeRange(slot: Slot, timeZone: string): string {
  return `${formatTime(slot.starts_at, timeZone)}–${formatTime(slot.ends_at, timeZone)}`
}

export function formatSlot(slot: Slot, timeZone: string): string {
  return `${formatDay(slot.starts_at, timeZone)}, ${formatTimeRange(slot, timeZone)}`
}
