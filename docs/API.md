# API Documentation - Compteo Tunisia

## Base URL

```
http://localhost:8000/api
```

## Authentication

All protected endpoints require authentication via Laravel Sanctum tokens.

### Headers

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

## Endpoints

### Authentication

#### Login
```http
POST /api/auth/login
```

**Request:**
```json
{
  "email": "demo@compteo.tn",
  "password": "demo"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Employé Demo",
    "email": "demo@compteo.tn",
    "role": "employee"
  },
  "token": "1|xxxxxxxxxxxxx"
}
```

#### Register
```http
POST /api/auth/register
```

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "organization_name": "My Company SARL"
}
```

#### Logout
```http
POST /api/auth/logout
```

#### Get Current User
```http
GET /api/auth/user
```

#### Refresh Token
```http
POST /api/auth/refresh
```

---

### Expense Reports

#### List Expense Reports
```http
GET /api/expense-reports
```

**Query Parameters:**
- `status`: draft, submitted, approved, rejected, paid
- `page`: Page number
- `per_page`: Items per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "reference": "ER-2025-0001",
      "title": "Déplacement Tunis - Sfax",
      "status": "submitted",
      "total_amount": 150.500,
      "total_ht": 126.470,
      "total_tva": 24.030,
      "currency": "TND",
      "submitted_at": "2025-11-17T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "Employé Demo",
        "email": "demo@compteo.tn"
      }
    }
  ],
  "meta": { ... }
}
```

#### Create Expense Report
```http
POST /api/expense-reports
```

**Request:**
```json
{
  "title": "Déplacement Tunis - Sfax",
  "description": "Mission commerciale"
}
```

#### Get Expense Report
```http
GET /api/expense-reports/{id}
```

#### Update Expense Report
```http
PUT /api/expense-reports/{id}
```

**Request:**
```json
{
  "title": "Déplacement modifié",
  "description": "Description mise à jour"
}
```

#### Delete Expense Report
```http
DELETE /api/expense-reports/{id}
```

#### Submit Expense Report
```http
POST /api/expense-reports/{id}/submit
```

**Response:**
```json
{
  "message": "Rapport de frais soumis avec succès",
  "expense_report": { ... }
}
```

#### Approve Expense Report
```http
POST /api/expense-reports/{id}/approve
```

**Response:**
```json
{
  "message": "Rapport de frais approuvé avec succès",
  "expense_report": { ... }
}
```

#### Reject Expense Report
```http
POST /api/expense-reports/{id}/reject
```

**Request:**
```json
{
  "reason": "Justificatifs manquants"
}
```

**Response:**
```json
{
  "message": "Rapport de frais rejeté avec succès",
  "expense_report": { ... }
}
```

#### Mark as Paid
```http
POST /api/expense-reports/{id}/pay
```

---

### Expense Items

#### List Items
```http
GET /api/expense-reports/{reportId}/items
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "date": "2025-11-17",
      "merchant_name": "Restaurant Le Gourmet",
      "amount": 45.500,
      "amount_ht": 38.235,
      "tva_rate": 19,
      "tva_amount": 7.265,
      "category": {
        "id": 2,
        "name": "Restaurant",
        "icon": "🍽️",
        "color": "#EF4444"
      }
    }
  ]
}
```

#### Create Item
```http
POST /api/expense-reports/{reportId}/items
```

**Request:**
```json
{
  "expense_category_id": 1,
  "date": "2025-11-17",
  "merchant_name": "Restaurant Le Gourmet",
  "amount": 45.500,
  "tva_rate": 19,
  "description": "Repas client"
}
```

**Response:**
```json
{
  "id": 1,
  "date": "2025-11-17",
  "merchant_name": "Restaurant Le Gourmet",
  "amount": 45.500,
  "amount_ht": 38.235,
  "tva_rate": 19,
  "tva_amount": 7.265,
  "category": { ... }
}
```

#### Get Item
```http
GET /api/expense-items/{id}
```

#### Update Item
```http
PUT /api/expense-items/{id}
```

**Request:**
```json
{
  "amount": 50.000,
  "description": "Repas client - Mise à jour"
}
```

#### Delete Item
```http
DELETE /api/expense-items/{id}
```

---

### Vehicles

#### List Vehicles
```http
GET /api/vehicles
```

**Query Parameters:**
- `active`: Filter by active status (true/false)
- `user_id`: Filter by user (personal vehicles)
- `type`: Filter by type (personal or company)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Renault Clio - 123 TU 1234",
      "brand": "Renault",
      "model": "Clio",
      "registration_number": "123 TU 1234",
      "fiscal_power": 5,
      "fuel_type": "essence",
      "type": "personal",
      "is_active": true
    }
  ]
}
```

#### Create Vehicle
```http
POST /api/vehicles
```

**Request:**
```json
{
  "name": "Renault Clio - 123 TU 1234",
  "brand": "Renault",
  "model": "Clio",
  "registration_number": "123 TU 1234",
  "fiscal_power": 5,
  "fuel_type": "essence",
  "type": "personal"
}
```

**Fuel Types:** `essence`, `diesel`, `gpl`, `electrique`, `hybride`
**Types:** `personal`, `company`

#### Get Vehicle
```http
GET /api/vehicles/{id}
```

#### Update Vehicle
```http
PUT /api/vehicles/{id}
```

**Request:**
```json
{
  "is_active": false
}
```

#### Delete Vehicle
```http
DELETE /api/vehicles/{id}
```

**Note:** Cannot delete vehicles that have associated mileage expenses.

#### Get Mileage Rate
```http
GET /api/vehicles/{id}/mileage-rate?annual_km=8000
```

**Response:**
```json
{
  "vehicle_id": 1,
  "fiscal_power": 5,
  "annual_km": 8000,
  "rate": 0.290,
  "currency": "TND"
}
```

**Mileage Rate Brackets (Tunisia 2025):**
- 0-5000 km: Higher rate
- 5001-10000 km: Medium rate
- 10001+ km: Lower rate

---

### Mileage Expenses

#### Calculate Distance
```http
POST /api/mileage/calculate-distance
```

**Request:**
```json
{
  "start_location": "Tunis",
  "end_location": "Sfax"
}
```

**Response:**
```json
{
  "start_location": "Tunis",
  "end_location": "Sfax",
  "distance_km": 272,
  "status": "mock"
}
```

**Note:** Mock implementation. In production, integrate with Google Maps Distance Matrix API.

#### List Mileage Expenses
```http
GET /api/expense-reports/{reportId}/mileage-expenses
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "date": "2025-11-17",
      "start_location": "Tunis",
      "end_location": "Sfax",
      "distance_km": 272,
      "round_trip": true,
      "fiscal_power": 5,
      "rate_per_km": 0.290,
      "total_amount": 157.760,
      "vehicle": {
        "id": 1,
        "name": "Renault Clio - 123 TU 1234"
      }
    }
  ]
}
```

#### Create Mileage Expense
```http
POST /api/expense-reports/{reportId}/mileage-expenses
```

**Request:**
```json
{
  "vehicle_id": 1,
  "date": "2025-11-17",
  "start_location": "Tunis",
  "end_location": "Sfax",
  "distance_km": 272,
  "round_trip": true,
  "purpose": "Visite client"
}
```

**Response:**
```json
{
  "id": 1,
  "date": "2025-11-17",
  "start_location": "Tunis",
  "end_location": "Sfax",
  "distance_km": 272,
  "round_trip": true,
  "fiscal_power": 5,
  "rate_per_km": 0.290,
  "total_amount": 157.760,
  "currency": "TND"
}
```

**Note:** Rate and amount are calculated automatically based on vehicle fiscal power.

#### Get Mileage Expense
```http
GET /api/mileage-expenses/{id}
```

#### Update Mileage Expense
```http
PUT /api/mileage-expenses/{id}
```

**Request:**
```json
{
  "distance_km": 280,
  "description": "Mise à jour du kilométrage"
}
```

#### Delete Mileage Expense
```http
DELETE /api/mileage-expenses/{id}
```

---

### Media

#### Upload File
```http
POST /api/media/upload
```

**Request (multipart/form-data):**
```
file: [binary]
mediable_type: App\Models\ExpenseItem
mediable_id: 1
```

**Supported File Types:** jpg, jpeg, png, pdf, gif
**Max File Size:** 10 MB

**Response:**
```json
{
  "id": 1,
  "file_name": "receipt.jpg",
  "file_url": "/storage/organizations/1/media/abc123.jpg",
  "mime_type": "image/jpeg",
  "size": 245680,
  "size_human": "240 KB",
  "is_image": true,
  "is_pdf": false,
  "ocr_status": "pending",
  "uploader": {
    "id": 1,
    "name": "Employé Demo"
  }
}
```

#### Get Media
```http
GET /api/media/{id}
```

#### Download Media
```http
GET /api/media/{id}/download
```

**Response:** File download

#### Delete Media
```http
DELETE /api/media/{id}
```

**Response:**
```json
{
  "message": "Fichier supprimé avec succès"
}
```

#### Process OCR
```http
POST /api/media/{id}/ocr
```

**Response:**
```json
{
  "message": "OCR traité avec succès",
  "data": {
    "merchant_name": "Restaurant Le Gourmet",
    "amount": 45.50,
    "tva_amount": 7.27,
    "date": "2025-11-15",
    "confidence": 0.87,
    "raw_text": "RESTAURANT LE GOURMET\nDate: 15/11/2025\nMontant TTC: 45.50 TND\nTVA 19%: 7.27 TND"
  }
}
```

**Note:** Mock implementation. In production, integrate with Google Cloud Vision API or Tesseract.

---

### Dashboard

#### Get Dashboard Stats
```http
GET /api/dashboard
```

**Response:**
```json
{
  "stats": {
    "draft": 3,
    "submitted": 2,
    "approved": 5,
    "rejected": 0,
    "paid": 1,
    "total_month": 1250.500,
    "pending_approval": 2
  },
  "recent_reports": [
    {
      "id": 1,
      "reference": "ER-2025-0001",
      "title": "Déplacement Tunis - Sfax",
      "status": "submitted",
      "total_amount": 150.500
    }
  ]
}
```

#### Get Monthly Trends
```http
GET /api/dashboard/trends?months=6
```

**Query Parameters:**
- `months`: Number of months to retrieve (default: 6)

**Response:**
```json
{
  "trends": [
    {
      "month": "2025-06",
      "month_name": "June 2025",
      "total": 850.500
    },
    {
      "month": "2025-07",
      "month_name": "July 2025",
      "total": 1024.750
    }
  ]
}
```

#### Get Category Breakdown
```http
GET /api/dashboard/category-breakdown
```

**Response:**
```json
{
  "breakdown": [
    {
      "name": "Transport",
      "icon": "🚗",
      "color": "#3B82F6",
      "count": 15,
      "total": 450.500
    },
    {
      "name": "Restaurant",
      "icon": "🍽️",
      "color": "#EF4444",
      "count": 8,
      "total": 320.750
    }
  ]
}
```

---

## Status Codes

- `200 OK`: Success
- `201 Created`: Resource created
- `204 No Content`: Success (no content)
- `400 Bad Request`: Invalid input
- `401 Unauthorized`: Not authenticated
- `403 Forbidden`: Not authorized
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error
- `500 Internal Server Error`: Server error

## Error Response Format

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Test Accounts

| Email | Password | Role |
|-------|----------|------|
| demo@compteo.tn | demo | employee |
| manager@compteo.tn | demo | manager |
| comptable@compteo.tn | demo | accountant |
| daf@compteo.tn | demo | daf |
| admin@compteo.tn | demo | admin |

## Tunisia-Specific Features

### TVA Rates
- 19% (standard rate)
- 13% (reduced rate)
- 7% (super-reduced rate)
- 0% (zero-rated)

### Mileage Barèmes 2025

| Fiscal Power | 0-5000 km | 5001-10000 km | 10001+ km |
|--------------|-----------|---------------|-----------|
| 4 CV | 0.280 TND/km | 0.260 TND/km | 0.240 TND/km |
| 5 CV | 0.310 TND/km | 0.290 TND/km | 0.270 TND/km |
| 6 CV | 0.340 TND/km | 0.320 TND/km | 0.300 TND/km |
| 7 CV | 0.370 TND/km | 0.350 TND/km | 0.330 TND/km |
| 8+ CV | 0.400 TND/km | 0.380 TND/km | 0.360 TND/km |

### Expense Categories (Plan Comptable SCE)

1. **Transport** (625) - 🚗
2. **Restaurant** (6253) - 🍽️
3. **Hôtel** (6251) - 🏨
4. **Carburant** (6252) - ⛽
5. **Parking** (6251) - 🅿️
6. **Fournitures** (606) - 📦
7. **Téléphone** (626) - 📱
8. **Internet** (626) - 🌐
9. **Formation** (617) - 📚
10. **Autre** (625) - 📋
