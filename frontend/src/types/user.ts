export interface User {
  id: number
  name: string
  email: string
  role: 'employee' | 'manager' | 'accountant' | 'daf' | 'admin'
  organization_id: number
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}
