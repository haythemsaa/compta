# Compteo Tunisia - Expense Management SaaS

**Compteo** is a modern, comprehensive expense management SaaS platform specifically designed for the Tunisian market. It helps businesses manage employee expense reports, mileage claims, receipt tracking with OCR, and automated accounting integration.

## Features

### Core Functionality
- **Expense Report Management**: Complete workflow from draft to payment
- **Multi-Category Expenses**: 10 pre-configured expense categories aligned with Tunisian accounting standards (Plan Comptable SCE)
- **Mileage Tracking**: Automatic calculation based on Tunisian 2025 barèmes kilométriques
- **Receipt Management**: Upload, OCR processing, and attachment to expenses
- **Multi-Role Access Control**: Employee, Manager, Accountant, DAF, Admin roles
- **Multi-Tenant SaaS**: Organization-based data isolation

### Tunisia-Specific Features
- **TVA Handling**: Automatic calculation with support for 19%, 13%, 7%, and 0% rates
- **Mileage Rates**: 2025 Tunisian government barèmes (4-8+ CV)
- **Plan Comptable SCE**: Accounting codes for each expense category
- **Matricule Fiscal**: Validation and tracking
- **TND Currency**: Default currency with multi-currency support
- **Bilingual**: French/Arabic support (planned)

### Technical Highlights
- **Modern Stack**: Laravel 11, Vue.js 3, TypeScript, PostgreSQL
- **RESTful API**: Complete API with Sanctum authentication
- **Real-time Dashboard**: Statistics, trends, and category breakdowns
- **OCR Integration**: Receipt scanning (mock implementation, ready for Google Cloud Vision/Tesseract)
- **Mobile-Ready**: Responsive design + Flutter app (planned)

## Project Structure

```
compta/
├── backend/              # Laravel 11.x API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/  # API controllers
│   │   │   ├── Resources/        # JSON resources
│   │   │   └── Requests/         # Form validation
│   │   └── Models/               # Eloquent models
│   ├── database/
│   │   ├── migrations/           # Database schema
│   │   └── seeders/              # Demo data
│   └── routes/api.php            # API routes
│
├── frontend/             # Vue.js 3 + TypeScript
│   ├── src/
│   │   ├── components/           # Vue components
│   │   ├── views/                # Page views
│   │   ├── services/             # API integration
│   │   └── stores/               # Pinia state management
│   └── public/
│
├── docs/                 # Documentation
│   ├── API.md                    # Complete API reference
│   ├── ARCHITECTURE.md           # Technical architecture
│   └── DEVELOPMENT.md            # Development guide
│
└── docker-compose.yml    # Development environment
```

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Node.js 18+ (for local frontend development)
- PHP 8.3+ (for local backend development)

### Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/haythemsaa/compta.git
cd compta

# Start all services
docker-compose up -d

# Install backend dependencies
docker-compose exec compteo-backend composer install

# Run migrations and seeders
docker-compose exec compteo-backend php artisan migrate --seed

# Install frontend dependencies
docker-compose exec compteo-frontend npm install

# Access the application
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000
# Mailhog: http://localhost:8025
```

### Local Development

#### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Configure environment
cp .env.example .env
# Edit .env with your database credentials

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start development server
php artisan serve
```

#### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

## Demo Accounts

After running seeders, you can log in with these test accounts:

| Email | Password | Role | Description |
|-------|----------|------|-------------|
| demo@compteo.tn | demo | Employee | Submit expense reports |
| manager@compteo.tn | demo | Manager | Approve/reject reports |
| comptable@compteo.tn | demo | Accountant | View all reports, export |
| daf@compteo.tn | demo | DAF | Full financial oversight |
| admin@compteo.tn | demo | Admin | System administration |

## API Documentation

Complete API documentation is available in [docs/API.md](docs/API.md).

**Key Endpoints:**
- `POST /api/auth/login` - Authentication
- `GET /api/expense-reports` - List expense reports
- `POST /api/expense-reports/{id}/submit` - Submit report for approval
- `POST /api/media/upload` - Upload receipts
- `POST /api/media/{id}/ocr` - Process OCR
- `GET /api/dashboard` - Dashboard statistics

## Database Schema

**Main Tables:**
- `organizations` - Multi-tenant companies
- `users` - Employees with role-based access
- `expense_reports` - Expense report headers
- `expense_items` - Individual expenses with TVA
- `mileage_expenses` - Mileage claims
- `vehicles` - Company and personal vehicles
- `expense_categories` - Expense types (Transport, Restaurant, etc.)
- `media` - Uploaded receipts and documents

## Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP**: 8.3+
- **Database**: PostgreSQL 16
- **Cache**: Redis 7
- **Authentication**: Laravel Sanctum
- **File Storage**: Local/S3 compatible

### Frontend
- **Framework**: Vue.js 3.5
- **Language**: TypeScript 5.x
- **Build Tool**: Vite 6.x
- **State Management**: Pinia 2.3
- **Routing**: Vue Router 4.5
- **Styling**: Tailwind CSS 3.4
- **HTTP Client**: Axios

### DevOps
- **Containerization**: Docker
- **Database**: PostgreSQL
- **Reverse Proxy**: Nginx (production)
- **Email**: Mailhog (development)

## Development Workflow

### Adding a New Feature

1. **Backend**:
   ```bash
   # Create migration
   php artisan make:migration create_feature_table

   # Create model
   php artisan make:model Feature

   # Create controller
   php artisan make:controller Api/FeatureController

   # Run migration
   php artisan migrate
   ```

2. **Frontend**:
   ```bash
   # Create component
   # Add to src/components/Feature.vue

   # Add route
   # Update src/router/index.ts
   ```

3. **Test**:
   ```bash
   # Backend tests
   php artisan test

   # Frontend tests
   npm run test
   ```

### Coding Standards

- **Backend**: PSR-12, Laravel best practices
- **Frontend**: Vue.js 3 Composition API, TypeScript strict mode
- **Git**: Conventional Commits

## Roadmap

### Phase 1 - MVP (Current)
- [x] Backend API with all controllers
- [x] Database schema and migrations
- [x] Authentication and authorization
- [x] Expense report workflow
- [x] Mileage calculation
- [x] Media upload
- [ ] Frontend integration
- [ ] Basic testing

### Phase 2 - Enhancement
- [ ] OCR integration (Google Cloud Vision)
- [ ] Distance calculation (Google Maps API)
- [ ] Email notifications
- [ ] PDF export for reports
- [ ] Advanced filtering and search
- [ ] Batch operations

### Phase 3 - Advanced
- [ ] Mobile app (Flutter)
- [ ] Accounting software integration
- [ ] Advanced analytics and reporting
- [ ] Multi-currency support
- [ ] Approval workflows customization
- [ ] Budget tracking

### Phase 4 - Enterprise
- [ ] SSO/LDAP integration
- [ ] Advanced audit trails
- [ ] Custom expense policies
- [ ] API webhooks
- [ ] White-label capabilities

## Tunisia-Specific Configuration

### TVA Rates (2025)
```php
'tva_rates' => [
    19 => 'Taux normal',
    13 => 'Taux réduit',
    7 => 'Taux super-réduit',
    0 => 'Exonéré',
]
```

### Mileage Barèmes (2025)
```php
// TND per kilometer
'mileage_rates' => [
    4 => [0.280, 0.260, 0.240],  // 0-5000km, 5001-10000km, 10001+km
    5 => [0.310, 0.290, 0.270],
    6 => [0.340, 0.320, 0.300],
    7 => [0.370, 0.350, 0.330],
    8 => [0.400, 0.380, 0.360],
]
```

### Expense Categories (Plan Comptable SCE)
1. Transport (6251) - 🚗
2. Restaurant (6253) - 🍽️
3. Hôtel (6251) - 🏨
4. Carburant (6252) - ⛽
5. Parking (6251) - 🅿️
6. Fournitures (606) - 📦
7. Téléphone (626) - 📱
8. Internet (626) - 🌐
9. Formation (617) - 📚
10. Autre (625) - 📋

## Security

### Best Practices Implemented
- **Authentication**: Laravel Sanctum tokens
- **Authorization**: Policy-based access control
- **SQL Injection**: Eloquent ORM with prepared statements
- **XSS**: Vue.js automatic escaping
- **CSRF**: Token validation
- **File Upload**: Type and size validation
- **Rate Limiting**: API throttling
- **Data Isolation**: Multi-tenant filtering

### Environment Variables
Never commit sensitive data. Use `.env` for:
- Database credentials
- API keys (Google Maps, OCR)
- Session secrets
- Mail credentials

## Troubleshooting

### Common Issues

**Database connection failed**
```bash
# Check PostgreSQL is running
docker-compose ps

# Check .env database configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
```

**Migrations fail**
```bash
# Reset database
php artisan migrate:fresh --seed
```

**Frontend not connecting to API**
```bash
# Check VITE_API_URL in frontend/.env
VITE_API_URL=http://localhost:8000
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is proprietary software. All rights reserved.

## Support

For support and questions:
- **Documentation**: [docs/](docs/)
- **Issues**: GitHub Issues
- **Email**: support@compteo.tn (planned)

## Acknowledgments

- Built for the Tunisian market
- Follows SCE (Système Comptable des Entreprises) standards
- Implements 2025 government mileage barèmes
- Designed for multi-tenant SaaS architecture

---

**Compteo Tunisia** - Modern expense management for Tunisian businesses 🇹🇳
