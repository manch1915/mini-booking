export type SlotStatus = 'free' | 'booked' | 'past'

export interface Slot {
  id: number
  starts_at: string
  ends_at: string
  status: SlotStatus
  booking_id: number | null
}

export interface Schedule {
  slots: Slot[]
  timezone: string
}

export interface Customer {
  customer_name: string
  customer_email: string
}
