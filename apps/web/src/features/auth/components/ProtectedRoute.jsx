import { Navigate } from 'react-router-dom'
import { useAuth } from '../AuthProvider'

function ProtectedRoute({ children }) {
  const { isChecking, session } = useAuth()

  if (isChecking) {
    return <p>Verificando sesión...</p>
  }

  return session ? children : <Navigate to="/login" replace />
}

export default ProtectedRoute
