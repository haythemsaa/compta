// User types
export interface User {
  id: number
  organization_id: number
  name: string
  email: string
  phone?: string
  role: 'employee' | 'manager' | 'accountant' | 'daf' | 'admin'
  job_title?: string
  department?: string
  is_active: boolean
  last_login_at?: string
  created_at: string
  organization?: Organization
}

export interface Organization {
  id: number
  name: string
  legal_name?: string
  matricule_fiscal?: string
  currency: string
  default_tva_rate: number
  plan: 'essential' | 'business' | 'enterprise'
}

// Expense Report types
export interface ExpenseReport {
  id: number
  organization_id: number
  user_id: number
  reference: string
  title: string
  description?: string
  status: 'draft' | 'submitted' | 'approved' | 'rejected' | 'paid'
  total_amount: number
  total_ht: number
  total_tva: number
  currency: string
  submitted_at?: string
  approved_at?: string
  rejected_at?: string
  rejection_reason?: string
  paid_at?: string
  created_at: string
  updated_at: string
  user?: User
  approver?: User
  rejecter?: User
  items?: ExpenseItem[]
  mileage_expenses?: MileageExpense[]
}

export interface CreateExpenseReportData {
  title: string
  description?: string
}

export interface UpdateExpenseReportData {
  title?: string
  description?: string
}

// Expense Item types
export interface ExpenseItem {
  id: number
  expense_report_id: number
  expense_category_id: number
  date: string
  merchant_name: string
  merchant_vat_number?: string
  description?: string
  amount: number
  amount_ht: number
  tva_rate: number
  tva_amount: number
  currency: string
  guest_count?: number
  guest_names?: string[]
  created_at: string
  updated_at: string
  category?: ExpenseCategory
  media?: Media[]
}

export interface CreateExpenseItemData {
  expense_category_id: number
  date: string
  merchant_name: string
  merchant_vat_number?: string
  description?: string
  amount: number
  tva_rate?: number
  guest_count?: number
  guest_names?: string[]
}

// Expense Category types
export interface ExpenseCategory {
  id: number
  name: string
  code: string
  icon: string
  color: string
  description?: string
  accounting_code: string
  requires_justification: boolean
  is_active: boolean
  sort_order: number
}

// Vehicle types
export interface Vehicle {
  id: number
  organization_id: number
  user_id?: number
  name: string
  brand: string
  model: string
  registration_number: string
  fiscal_power: number
  fuel_type: 'essence' | 'diesel' | 'gpl' | 'electrique' | 'hybride'
  type: 'personal' | 'company'
  is_active: boolean
  created_at: string
  updated_at: string
  user?: User
}

export interface CreateVehicleData {
  user_id?: number
  name: string
  brand: string
  model: string
  registration_number: string
  fiscal_power: number
  fuel_type: 'essence' | 'diesel' | 'gpl' | 'electrique' | 'hybride'
  type: 'personal' | 'company'
}

export interface MileageRate {
  vehicle_id: number
  fiscal_power: number
  annual_km: number
  rate: number
  currency: string
}

// Mileage Expense types
export interface MileageExpense {
  id: number
  expense_report_id: number
  vehicle_id: number
  date: string
  start_location: string
  end_location: string
  distance_km: number
  round_trip: boolean
  fiscal_power: number
  rate_per_km: number
  total_amount: number
  currency: string
  description?: string
  purpose?: string
  created_at: string
  updated_at: string
  vehicle?: Vehicle
}

export interface CreateMileageExpenseData {
  vehicle_id: number
  date: string
  start_location: string
  end_location: string
  distance_km: number
  round_trip?: boolean
  description?: string
  purpose?: string
}

export interface DistanceCalculation {
  start_location: string
  end_location: string
  distance_km: number
  status: 'mock' | 'calculated'
}

// Media types
export interface Media {
  id: number
  mediable_type: string
  mediable_id: number
  organization_id: number
  uploaded_by: number
  file_name: string
  file_path: string
  file_url?: string
  mime_type: string
  size: number
  size_human?: string
  is_image: boolean
  is_pdf: boolean
  ocr_status: 'pending' | 'processing' | 'completed' | 'failed'
  ocr_data?: OcrData
  ocr_processed_at?: string
  created_at: string
  updated_at: string
  uploader?: User
}

export interface OcrData {
  merchant_name?: string
  amount?: number
  tva_amount?: number
  date?: string
  confidence?: number
  raw_text?: string
}

// Dashboard types
export interface DashboardStats {
  draft: number
  submitted: number
  approved: number
  rejected: number
  paid: number
  total_month: number
  pending_approval: number
}

export interface DashboardData {
  stats: DashboardStats
  recent_reports: ExpenseReport[]
}

export interface MonthlyTrend {
  month: string
  month_name: string
  total: number
}

export interface CategoryBreakdown {
  name: string
  icon: string
  color: string
  count: number
  total: number
}

// API Response types
export interface ApiResponse<T> {
  data: T
  message?: string
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    from: number
    last_page: number
    per_page: number
    to: number
    total: number
  }
}

export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}
