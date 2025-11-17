export interface ExpenseReport {
  id: number
  user_id: number
  organization_id: number
  title: string
  description?: string
  total_amount: number
  currency: 'TND' | 'EUR' | 'USD'
  status: 'draft' | 'submitted' | 'approved' | 'rejected' | 'paid'
  submitted_at?: string
  approved_at?: string
  rejected_at?: string
  paid_at?: string
  items: ExpenseItem[]
  created_at: string
  updated_at: string
}

export interface ExpenseItem {
  id: number
  expense_report_id: number
  category: ExpenseCategory
  amount: number
  amount_ht?: number
  tva_rate?: number
  tva_amount?: number
  date: string
  description?: string
  merchant_name?: string
  merchant_vat_number?: string
  media?: Media[]
  created_at: string
  updated_at: string
}

export interface Media {
  id: number
  file_path: string
  file_name: string
  mime_type: string
  size: number
  ocr_data?: OCRData
  created_at: string
}

export interface OCRData {
  merchant_name?: string
  merchant_vat_number?: string
  date?: string
  amount_ttc?: number
  amount_ht?: number
  tva_rate?: number
  tva_amount?: number
  currency?: string
  confidence: number
}

export type ExpenseCategory =
  | 'transport'
  | 'restaurant'
  | 'hotel'
  | 'fuel'
  | 'parking'
  | 'supplies'
  | 'phone'
  | 'internet'
  | 'software'
  | 'training'
  | 'other'

export interface MileageExpense {
  id: number
  expense_report_id: number
  vehicle_id: number
  start_location: string
  end_location: string
  distance_km: number
  rate_per_km: number
  total_amount: number
  date: string
  description?: string
  round_trip: boolean
  created_at: string
  updated_at: string
}
