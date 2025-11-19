#!/bin/bash

###############################################################################
# Compteo TN - Automated Deployment Script
# Deploy to production with zero-downtime
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
APP_DIR="/var/www/compteo-tn"
BACKUP_DIR="/var/backups/compteo-tn"
DATE=$(date +%Y%m%d_%H%M%S)

print_header() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

print_success() { echo -e "${GREEN}✓${NC} $1"; }
print_error() { echo -e "${RED}✗${NC} $1"; }
print_info() { echo -e "${YELLOW}ℹ${NC} $1"; }
print_step() { echo -e "${BLUE}➤${NC} $1"; }

# Pre-deployment checks
pre_deploy_checks() {
    print_header "Pre-Deployment Checks"

    # Check if running as correct user
    if [ "$EUID" -eq 0 ]; then
        print_error "Do not run this script as root"
        exit 1
    fi

    # Check if app directory exists
    if [ ! -d "$APP_DIR" ]; then
        print_error "Application directory not found: $APP_DIR"
        exit 1
    fi

    # Check if .env exists
    if [ ! -f "$APP_DIR/backend/.env" ]; then
        print_error "Backend .env file not found"
        exit 1
    fi

    if [ ! -f "$APP_DIR/frontend/.env" ]; then
        print_error "Frontend .env file not found"
        exit 1
    fi

    print_success "Pre-deployment checks passed"
}

# Create backup
create_backup() {
    print_header "Creating Backup"

    mkdir -p "$BACKUP_DIR"

    print_step "Backing up database..."
    docker-compose exec -T postgres pg_dump -U compteo compteo_tunisia > "$BACKUP_DIR/db_$DATE.sql"
    print_success "Database backed up"

    print_step "Backing up uploaded files..."
    tar -czf "$BACKUP_DIR/storage_$DATE.tar.gz" "$APP_DIR/backend/storage/app"
    print_success "Files backed up"

    print_info "Backup location: $BACKUP_DIR"
}

# Pull latest code
pull_code() {
    print_header "Pulling Latest Code"

    cd "$APP_DIR"

    print_step "Fetching from Git..."
    git fetch origin

    print_step "Pulling changes..."
    git pull origin main

    print_success "Code updated"
}

# Update backend
update_backend() {
    print_header "Updating Backend"

    cd "$APP_DIR/backend"

    print_step "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    print_success "Composer dependencies updated"

    print_step "Clearing config cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    print_success "Cache cleared"

    print_step "Running migrations..."
    php artisan migrate --force
    print_success "Migrations completed"

    print_step "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    print_success "Application optimized"
}

# Update frontend
update_frontend() {
    print_header "Updating Frontend"

    cd "$APP_DIR/frontend"

    print_step "Installing npm dependencies..."
    npm ci --production
    print_success "npm dependencies updated"

    print_step "Building frontend..."
    npm run build
    print_success "Frontend built"
}

# Restart services
restart_services() {
    print_header "Restarting Services"

    print_step "Restarting queue workers..."
    php artisan queue:restart
    print_success "Queue workers restarted"

    print_step "Restarting PHP-FPM..."
    sudo systemctl reload php8.3-fpm
    print_success "PHP-FPM restarted"

    print_step "Reloading nginx..."
    sudo nginx -t && sudo systemctl reload nginx
    print_success "nginx reloaded"
}

# Health check
health_check() {
    print_header "Health Check"

    print_step "Checking backend..."
    BACKEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/health)

    if [ "$BACKEND_STATUS" == "200" ]; then
        print_success "Backend is healthy"
    else
        print_error "Backend health check failed (HTTP $BACKEND_STATUS)"
        exit 1
    fi

    print_step "Checking frontend..."
    FRONTEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:3000)

    if [ "$FRONTEND_STATUS" == "200" ]; then
        print_success "Frontend is healthy"
    else
        print_error "Frontend health check failed (HTTP $FRONTEND_STATUS)"
        exit 1
    fi

    print_success "All services are healthy"
}

# Cleanup old backups (keep last 10)
cleanup_backups() {
    print_header "Cleaning Up Old Backups"

    cd "$BACKUP_DIR"

    print_step "Removing old database backups..."
    ls -t db_*.sql | tail -n +11 | xargs -r rm
    print_success "Old database backups removed"

    print_step "Removing old file backups..."
    ls -t storage_*.tar.gz | tail -n +11 | xargs -r rm
    print_success "Old file backups removed"
}

# Send deployment notification
send_notification() {
    print_header "Deployment Complete!"

    echo -e "${GREEN}✓ Deployment successful${NC}"
    echo "  • Date: $(date)"
    echo "  • Backup: $BACKUP_DIR"
    echo "  • Git commit: $(git rev-parse --short HEAD)"
    echo ""

    # Optional: Send to Slack/Discord/Email
    # curl -X POST -H 'Content-type: application/json' \
    #     --data '{"text":"Compteo TN deployed successfully"}' \
    #     YOUR_WEBHOOK_URL
}

# Rollback function
rollback() {
    print_header "Rolling Back Deployment"

    print_error "Deployment failed! Rolling back..."

    cd "$APP_DIR"

    print_step "Reverting Git changes..."
    git reset --hard HEAD@{1}

    print_step "Restoring database..."
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/db_*.sql | head -1)
    docker-compose exec -T postgres psql -U compteo compteo_tunisia < "$LATEST_BACKUP"

    print_step "Restoring application..."
    cd backend
    composer install --no-dev
    php artisan migrate:rollback
    php artisan config:cache

    cd ../frontend
    npm ci --production
    npm run build

    restart_services

    print_info "Rollback complete. Please check application status."
}

# Main deployment flow
main() {
    print_header "Compteo TN - Deployment"

    # Trap errors and rollback
    trap rollback ERR

    pre_deploy_checks
    create_backup
    pull_code
    update_backend
    update_frontend
    restart_services
    health_check
    cleanup_backups
    send_notification
}

# Run main function
main
