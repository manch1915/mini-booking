const NETWORK_ERROR = 0

type FieldErrors = Record<string, string[]>

export class ApiError extends Error {
  readonly status: number
  readonly fieldErrors: FieldErrors

  constructor(status: number, message: string, fieldErrors: FieldErrors = {}) {
    super(message)
    this.status = status
    this.fieldErrors = fieldErrors
  }

  get isValidation(): boolean {
    return this.status === 422
  }

  get isConflict(): boolean {
    return this.status === 409
  }

  get isNotFound(): boolean {
    return this.status === 404
  }
}

export async function request<T>(method: string, path: string, body?: object): Promise<T> {
  let response: Response

  try {
    response = await fetch(`/api${path}`, {
      method,
      headers: {
        Accept: 'application/json',
        ...(body && { 'Content-Type': 'application/json' }),
      },
      body: body && JSON.stringify(body),
    })
  } catch {
    throw new ApiError(NETWORK_ERROR, 'Нет связи с сервером. Проверьте соединение и попробуйте ещё раз.')
  }

  if (response.status === 204) {
    return undefined as T
  }

  const payload = await response.json().catch(() => null)

  if (!response.ok) {
    throw new ApiError(response.status, errorMessage(response.status, payload), payload?.errors)
  }

  return payload
}

function errorMessage(status: number, payload: { message?: string } | null): string {
  if (status === 409 && payload?.message) {
    return payload.message
  }

  if (status === 422) {
    return 'Проверьте поля формы.'
  }

  return 'Сервер не смог обработать запрос. Попробуйте ещё раз.'
}
