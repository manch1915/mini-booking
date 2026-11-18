import type { Customer, Schedule, Slot } from '../types'
import { request } from './http'

interface SlotsResponse {
  data: Slot[]
  meta: { timezone: string }
}

export async function fetchSchedule(): Promise<Schedule> {
  const { data, meta } = await request<SlotsResponse>('GET', '/slots')

  return { slots: data, timezone: meta.timezone }
}

export function createBooking(slotId: number, customer: Customer): Promise<{ id: number }> {
  return request('POST', '/bookings', { slot_id: slotId, ...customer })
}

export function cancelBooking(bookingId: number): Promise<void> {
  return request('DELETE', `/bookings/${bookingId}`)
}
