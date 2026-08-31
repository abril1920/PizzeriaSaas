import { createContext, useContext, useEffect, useState } from 'react'
import { getCurrentUser, logout } from './api/authApi'
import { clearSession, getSession, saveSession } from './authSession'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [session, setSession] = useState(getSession)
  const [isChecking, setIsChecking] = useState(true)

  useEffect(() => {
    async function verifySession() {
      if (!session?.token) {
        setIsChecking(false)
        return
      }

      try {
        const { user } = await getCurrentUser()
        const verifiedSession = { ...session, user }
        saveSession(verifiedSession)
        setSession(verifiedSession)
      } catch {
        clearSession()
        setSession(null)
      } finally {
        setIsChecking(false)
      }
    }

    verifySession()
  }, [])

  function startSession(nextSession) {
    saveSession(nextSession)
    setSession(nextSession)
  }

  async function endSession() {
    try {
      await logout()
    } finally {
      clearSession()
      setSession(null)
    }
  }

  return (
    <AuthContext.Provider value={{ session, isChecking, startSession, endSession }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const context = useContext(AuthContext)

  if (!context) {
    throw new Error('useAuth debe usarse dentro de AuthProvider.')
  }

  return context
}
