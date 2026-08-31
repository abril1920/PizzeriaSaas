import { useNavigate } from 'react-router-dom'
import { useAuth } from '../features/auth/AuthProvider'

function DashboardPage() {
  const navigate = useNavigate()
  const { endSession, session } = useAuth()

  async function handleLogout() {
    await endSession()
    navigate('/login', { replace: true })
  }

  return (
    <main>
      <h1>Dashboard</h1>
      <p>Sesión iniciada como {session.user.nombre}.</p>
      <button type="button" onClick={handleLogout}>Cerrar sesión</button>
    </main>
  )
}

export default DashboardPage
