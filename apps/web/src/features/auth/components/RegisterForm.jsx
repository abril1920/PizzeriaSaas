import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { register } from '../api/authApi'
import { useAuth } from '../AuthProvider'
import { getAuthErrorMessage } from '../getAuthErrorMessage'

const initialAccount = {
  empresa_nombre: '',
  nit: '',
  nombre: '',
  apellido: '',
  correo: '',
  password: '',
  password_confirmation: '',
}

function RegisterForm() {
  const navigate = useNavigate()
  const { startSession } = useAuth()
  const [account, setAccount] = useState(initialAccount)
  const [error, setError] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  function updateField(event) {
    const { name, value } = event.target
    setAccount((current) => ({ ...current, [name]: value }))
  }

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')

    if (account.password !== account.password_confirmation) {
      setError('Las contraseñas deben coincidir.')
      return
    }

    setIsSubmitting(true)

    try {
      const session = await register(account)
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
      <label htmlFor="empresa_nombre">Nombre de la pizzería</label>
      <input id="empresa_nombre" name="empresa_nombre" value={account.empresa_nombre} onChange={updateField} required autoComplete="organization" />

      <label htmlFor="nit">NIT</label>
      <input id="nit" name="nit" value={account.nit} onChange={updateField} required />

      <label htmlFor="nombre">Nombre</label>
      <input id="nombre" name="nombre" value={account.nombre} onChange={updateField} required autoComplete="given-name" />

      <label htmlFor="apellido">Apellido</label>
      <input id="apellido" name="apellido" value={account.apellido} onChange={updateField} autoComplete="family-name" />

      <label htmlFor="correo">Correo</label>
      <input id="correo" name="correo" type="email" value={account.correo} onChange={updateField} required autoComplete="email" />

      <label htmlFor="password">Contraseña</label>
      <input id="password" name="password" type="password" value={account.password} onChange={updateField} required minLength="10" autoComplete="new-password" />

      <label htmlFor="password_confirmation">Confirmar contraseña</label>
      <input id="password_confirmation" name="password_confirmation" type="password" value={account.password_confirmation} onChange={updateField} required minLength="10" autoComplete="new-password" />

      {error && <p role="alert">{error}</p>}

      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? 'Creando cuenta...' : 'Crear cuenta'}
      </button>
    </form>
  )
}

export default RegisterForm
