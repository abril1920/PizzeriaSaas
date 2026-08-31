
import { Link } from 'react-router-dom'
import LoginForm from '../features/auth/components/LoginForm'

function LoginPage() {
  return (
    <main>
      <h1>Iniciar sesión</h1>
      <LoginForm />
      <p>¿Aún no tienes cuenta? <Link to="/registro">Regístrate</Link></p>
    </main>
  )
}

export default LoginPage


