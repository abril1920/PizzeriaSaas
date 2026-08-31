const SESSION_KEY = 'pizzeria-auth-session'

export function saveSession(session) {
  localStorage.setItem(SESSION_KEY, JSON.stringify(session))
}

export function getSession() {
  const value = localStorage.getItem(SESSION_KEY)

  try {
    return value ? JSON.parse(value) : null
  } catch {
    localStorage.removeItem(SESSION_KEY)
    return null
  }
}

export function clearSession() {
  localStorage.removeItem(SESSION_KEY)
}
