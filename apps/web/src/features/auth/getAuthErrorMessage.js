export function getAuthErrorMessage(error) {
  const errors = error.response?.data?.errors

  if (errors) {
    return Object.values(errors).flat()[0]
  }

  return error.response?.data?.message ?? 'No fue posible completar la solicitud. Intenta de nuevo.'
}
