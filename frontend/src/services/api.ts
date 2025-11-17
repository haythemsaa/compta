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
  expenseReports = {
    list: (params?: any) => this.get('/expense-reports', { params }),
    get: (id: number) => this.get(`/expense-reports/${id}`),
    create: (data: any) => this.post('/expense-reports', data),
    update: (id: number, data: any) => this.put(`/expense-reports/${id}`, data),
    delete: (id: number) => this.delete(`/expense-reports/${id}`),
    submit: (id: number) => this.post(`/expense-reports/${id}/submit`),
    approve: (id: number) => this.post(`/expense-reports/${id}/approve`),
    reject: (id: number, reason: string) =>
      this.post(`/expense-reports/${id}/reject`, { reason }),
    pay: (id: number) => this.post(`/expense-reports/${id}/pay`)
  }

  // Expense items endpoints
  expenseItems = {
    list: (reportId: number) => this.get(`/expense-reports/${reportId}/items`),
    get: (id: number) => this.get(`/expense-items/${id}`),
    create: (reportId: number, data: any) =>
      this.post(`/expense-reports/${reportId}/items`, data),
    update: (id: number, data: any) => this.put(`/expense-items/${id}`, data),
    delete: (id: number) => this.delete(`/expense-items/${id}`)
  }

  // Expense categories endpoints
  categories = {
    list: (params?: any) => this.get('/expense-categories', { params }),
    get: (id: number) => this.get(`/expense-categories/${id}`)
  }

  // Vehicles endpoints
  vehicles = {
    list: (params?: any) => this.get('/vehicles', { params }),
    get: (id: number) => this.get(`/vehicles/${id}`),
    create: (data: any) => this.post('/vehicles', data),
    update: (id: number, data: any) => this.put(`/vehicles/${id}`, data),
    delete: (id: number) => this.delete(`/vehicles/${id}`),
    getMileageRate: (id: number, annualKm?: number) =>
      this.get(`/vehicles/${id}/mileage-rate`, { params: { annual_km: annualKm } })
  }

  // Mileage expenses endpoints
  mileageExpenses = {
    list: (reportId: number) => this.get(`/expense-reports/${reportId}/mileage-expenses`),
    get: (id: number) => this.get(`/mileage-expenses/${id}`),
    create: (reportId: number, data: any) =>
      this.post(`/expense-reports/${reportId}/mileage-expenses`, data),
    update: (id: number, data: any) => this.put(`/mileage-expenses/${id}`, data),
    delete: (id: number) => this.delete(`/mileage-expenses/${id}`),
    calculateDistance: (start: string, end: string) =>
      this.post('/mileage/calculate-distance', { start_location: start, end_location: end })
  }

  // Media endpoints
  media = {
    upload: (file: File, mediableType: string, mediableId: number) => {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('mediable_type', mediableType)
      formData.append('mediable_id', mediableId.toString())
      return this.post('/media/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    },
    get: (id: number) => this.get(`/media/${id}`),
    download: (id: number) => this.get(`/media/${id}/download`, { responseType: 'blob' }),
    delete: (id: number) => this.delete(`/media/${id}`),
    processOcr: (id: number) => this.post(`/media/${id}/ocr`)
  }

  // Dashboard endpoints
  dashboard = {
    stats: () => this.get('/dashboard'),
    trends: (months?: number) => this.get('/dashboard/trends', { params: { months } }),
    categoryBreakdown: () => this.get('/dashboard/category-breakdown')
  }
}

export const api = new ApiService()
export default api
