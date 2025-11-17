import axios from 'axios'
import type { AxiosInstance, AxiosRequestConfig } from 'axios'

class ApiService {
  private api: AxiosInstance

  constructor() {
    this.api = axios.create({
      baseURL: import.meta.env.VITE_API_URL || '/api',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    // Request interceptor
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        return config
      },
      (error) => {
        return Promise.reject(error)
      }
    )

    // Response interceptor
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Token expired or invalid
          localStorage.removeItem('auth_token')
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    )
  }

  // Generic methods
  async get<T>(url: string, config?: AxiosRequestConfig): Promise<T> {
    const response = await this.api.get<T>(url, config)
    return response.data
  }

  async post<T>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
    const response = await this.api.post<T>(url, data, config)
    return response.data
  }

  async put<T>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
    const response = await this.api.put<T>(url, data, config)
    return response.data
  }

  async delete<T>(url: string, config?: AxiosRequestConfig): Promise<T> {
    const response = await this.api.delete<T>(url, config)
    return response.data
  }

  // Auth endpoints
  auth = {
    login: (email: string, password: string) =>
      this.post('/auth/login', { email, password }),
    logout: () => this.post('/auth/logout'),
    register: (data: any) => this.post('/auth/register', data),
    me: () => this.get('/auth/user'),
    refresh: () => this.post('/auth/refresh')
  }

  // Expense reports endpoints
  expenses = {
    list: (params?: any) => this.get('/expense-reports', { params }),
    get: (id: number) => this.get(`/expense-reports/${id}`),
    create: (data: any) => this.post('/expense-reports', data),
    update: (id: number, data: any) => this.put(`/expense-reports/${id}`, data),
    delete: (id: number) => this.delete(`/expense-reports/${id}`),
    submit: (id: number) => this.post(`/expense-reports/${id}/submit`),
    approve: (id: number) => this.post(`/expense-reports/${id}/approve`),
    reject: (id: number, reason: string) =>
      this.post(`/expense-reports/${id}/reject`, { reason })
  }

  // OCR endpoints
  ocr = {
    process: (file: File) => {
      const formData = new FormData()
      formData.append('file', file)
      return this.post('/ocr/process', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    },
    status: (id: string) => this.get(`/ocr/status/${id}`)
  }

  // Mileage endpoints
  mileage = {
    calculateDistance: (start: string, end: string) =>
      this.post('/mileage/calculate-distance', { start_location: start, end_location: end }),
    rates: () => this.get('/mileage/rates'),
    create: (data: any) => this.post('/mileage/expenses', data)
  }
}

export const api = new ApiService()
export default api
