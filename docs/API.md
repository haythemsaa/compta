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

#### Logout
```http
POST /api/auth/logout
```

#### Get Current User
```http
GET /api/auth/user
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

#### Delete Expense Report
```http
DELETE /api/expense-reports/{id}
```

#### Submit Expense Report
```http
POST /api/expense-reports/{id}/submit
```

#### Approve Expense Report
```http
POST /api/expense-reports/{id}/approve
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

---

### Expense Items

#### List Items
```http
GET /api/expense-reports/{reportId}/items
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

#### Update Item
```http
PUT /api/expense-items/{id}
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

#### Get Media
```http
GET /api/media/{id}
```

#### Delete Media
```http
DELETE /api/media/{id}
```

#### Process OCR
```http
POST /api/media/{id}/ocr
```

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
    "pending": 3,
    "submitted": 2,
    "approved": 5,
    "rejected": 0,
    "total_month": 1250.500
  },
  "recent_reports": [...]
}
```

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
