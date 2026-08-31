import { Link } from 'react-router-dom'
import RegisterForm from '../features/auth/components/RegisterForm'

function RegisterPage() {
  return (
    <main>
      <h1>Crear cuenta</h1>
      <RegisterForm />
      <p>¿Ya tienes cuenta? <Link to="/login">Inicia sesión</Link></p>
    </main>
  )
}

export default RegisterPage
