import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { login } from '../api/authApi'
import { useAuth } from '../AuthProvider'
import { getAuthErrorMessage } from '../getAuthErrorMessage'

function LoginForm() {
  const navigate = useNavigate()
  const { startSession } = useAuth()
  const [credentials, setCredentials] = useState({ correo: '', password: '' })
  const [error, setError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  function updateField(event) {
    const { name, value } = event.target
    setCredentials((current) => ({ ...current, [name]: value }))
  }

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')
    setIsSubmitting(true)

    try {
      const session = await login(credentials)
      startSession(session)
      navigate('/dashboard', { replace: true })
    } catch (requestError) {
      setError(getAuthErrorMessage(requestError))
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <label htmlFor="correo">Correo</label>
      <input id="correo" name="correo" type="email" value={credentials.correo} onChange={updateField} required autoComplete="email" />

      <label htmlFor="password">Contraseña</label>
      <input id="password" name="password" type="password" value={credentials.password} onChange={updateField} required autoComplete="current-password" />

      {error && <p role="alert">{error}</p>}

      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? 'Ingresando...' : 'Ingresar'}
      </button>
    </form>
  )
}

export default LoginForm
