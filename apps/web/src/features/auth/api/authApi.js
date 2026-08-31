import axios from 'axios'
import { clearSession, getSession } from '../authSession'

const client = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://127.0.0.1:8000/api/v1',
  headers: { Accept: 'application/json' },
})

client.interceptors.request.use((config) => {
  const token = getSession()?.token

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

client.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      clearSession()
    }

    return Promise.reject(error)
  },
)

export async function login(credentials) {
  const { data } = await client.post('/login', credentials)
  return data
}

export async function register(account) {
  const { data } = await client.post('/register', account)
  return data
}

export async function getCurrentUser() {
  const { data } = await client.get('/me')
  return data
}

export async function logout() {
  await client.post('/logout')
}
