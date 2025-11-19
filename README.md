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

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose (recommended)
- OR: Node.js 18+, PHP 8.3+, PostgreSQL 16, Redis 7

### ⚡ Installation en 1 commande

```bash
git clone https://github.com/haythemsaa/compta.git
cd compta
chmod +x install.sh
./install.sh
```

Le script interactif vous guidera à travers :
- **Option 1** : Installation Docker (recommandée)
- **Option 2** : Installation locale
- **Option 3** : Configuration production

### Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/haythemsaa/compta.git
cd compta

# Start all services
docker-compose up -d

# Run migrations and seeders (with demo data)
docker-compose exec backend php artisan migrate --seed

# Access the application
# Frontend: http://localhost:3000
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

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get current user

### Expense Reports
- `GET /api/expense-reports` - List expense reports
- `POST /api/expense-reports` - Create new report
- `GET /api/expense-reports/{id}` - Get report details
- `PUT /api/expense-reports/{id}` - Update report
- `DELETE /api/expense-reports/{id}` - Delete report
- `POST /api/expense-reports/{id}/submit` - Submit for approval
- `POST /api/expense-reports/{id}/approve` - Approve report
- `POST /api/expense-reports/{id}/reject` - Reject report

### Export Endpoints ✨ NEW
- `GET /api/expense-reports/{id}/export/pdf` - Export single report as PDF
- `GET /api/expense-reports/{id}/export/excel` - Export single report as Excel
- `POST /api/expense-reports/export/excel` - Export multiple reports as Excel

### Other Endpoints
- `POST /api/media/upload` - Upload receipts
- `POST /api/media/{id}/ocr` - Process OCR
- `GET /api/dashboard` - Dashboard statistics
- `GET /api/dashboard/trends` - Expense trends
- `GET /api/dashboard/category-breakdown` - Category statistics
- `GET /api/expense-categories` - List categories
- `GET /api/vehicles` - List vehicles
- `POST /api/vehicles` - Add vehicle

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
- **Styling**: Bootstrap 5.3.2
- **Icons**: Bootstrap Icons 1.11.3 (1800+ icons)
- **Animations**: Animate.css 4.1.1
- **HTTP Client**: Axios

### DevOps
- **Containerization**: Docker
- **Database**: PostgreSQL
- **Reverse Proxy**: Nginx (production)
- **Email**: Mailhog (development)

## 🚀 Production Deployment

### Automated Deployment

```bash
# Deploy to production with one command
./deploy.sh
```

**The script automatically:**
1. ✅ Creates backup of database and files
2. ✅ Pulls latest code from Git
3. ✅ Installs/updates dependencies (Composer & npm)
4. ✅ Runs database migrations
5. ✅ Builds frontend for production
6. ✅ Clears and rebuilds caches
7. ✅ Restarts services (PHP-FPM, nginx, queues)
8. ✅ Performs health checks
9. ✅ Rolls back automatically on failure

### Automated Backups

```bash
# Manual backup
./backup.sh

# Schedule automatic backups (cron)
crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * /var/www/compteo-tn/backup.sh >> /var/log/compteo-backup.log 2>&1
```

**Backup includes:**
- PostgreSQL database (compressed)
- Redis data
- Uploaded files (storage/app)
- Environment configuration (encrypted)
- Weekly and monthly archives
- Optional S3 upload

### Nginx Configuration

```bash
# Copy nginx config
sudo cp nginx.conf /etc/nginx/sites-available/compteo-tn
sudo ln -s /etc/nginx/sites-available/compteo-tn /etc/nginx/sites-enabled/

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

**Features:**
- SSL/TLS with Let's Encrypt
- HTTP/2 support
- GZIP compression
- Static file caching
- Rate limiting (API: 60/min, Login: 5/min)
- Security headers (HSTS, CSP, etc.)

### Environment Configuration

See [backend/.env.production.example](backend/.env.production.example) for complete production configuration with 100+ variables including:
- Application settings
- Database (PostgreSQL)
- Cache & Queue (Redis)
- Email (SMTP, SendGrid, Mailgun)
- File storage (S3, DigitalOcean Spaces)
- Tunisia-specific settings (TVA, mileage rates)
- Security settings
- Third-party integrations
- Monitoring (Sentry, New Relic)
- Backup configuration

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

## 📦 Production Features

### ✅ Deployment & DevOps
- **One-command installation** (`install.sh`) - Interactive setup script
- **Automated deployment** (`deploy.sh`) - Zero-downtime deployments
- **Automated backups** (`backup.sh`) - Daily/weekly/monthly with S3 support
- **Nginx configuration** - Production-ready with SSL, caching, rate limiting
- **Docker Compose** - Full stack containerization
- **Environment templates** - Complete `.env.production.example` with 100+ variables

### 📊 Export & Reporting
- ✅ **PDF Export** - Professional HTML-based PDF exports
- ✅ **Excel/CSV Export** - Single or multiple reports
- ✅ **Email Templates** - Beautiful responsive email notifications
  - Report submitted (to approvers)
  - Report approved (to employee)
  - Report rejected (with reasons)
- ✅ **Real-time Dashboard** - Statistics and analytics

### 🗄️ Data & Backup
- ✅ **Complete Database Seeders** - 6 realistic expense reports with all statuses
- ✅ **Automated Backups** - PostgreSQL, Redis, files, environment
- ✅ **Backup Retention** - Daily (30d), Weekly (4w), Monthly (12m)
- ✅ **S3 Integration** - Optional cloud backup storage

## Roadmap

### Phase 1 - MVP ✅ COMPLETE
- [x] Backend API with all controllers
- [x] Database schema and migrations
- [x] Authentication and authorization
- [x] Expense report workflow
- [x] Mileage calculation
- [x] Media upload
- [x] Frontend with Bootstrap 5
- [x] PDF/Excel exports
- [x] Email notifications templates
- [x] Production deployment scripts
- [x] Complete documentation

### Phase 2 - Enhancement
- [ ] OCR integration (Google Cloud Vision)
- [ ] Distance calculation (Google Maps API)
- [ ] Automated testing (PHPUnit + Vitest)
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
