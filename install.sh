#!/bin/bash

###############################################################################
# Compteo TN - Installation Script
# One-command setup for development and production
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

print_step() {
    echo -e "${BLUE}➤${NC} $1"
}

# Check if Docker is installed
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        echo "Visit: https://docs.docker.com/get-docker/"
        exit 1
    fi

    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        echo "Visit: https://docs.docker.com/compose/install/"
        exit 1
    fi

    print_success "Docker and Docker Compose are installed"
}

# Check if Node.js is installed
check_node() {
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 18+ first."
        echo "Visit: https://nodejs.org/"
        exit 1
    fi

    NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
    if [ "$NODE_VERSION" -lt 18 ]; then
        print_error "Node.js version must be 18 or higher. Current: $(node -v)"
        exit 1
    fi

    print_success "Node.js $(node -v) is installed"
}

# Setup backend
setup_backend() {
    print_step "Setting up Laravel backend..."

    cd backend

    # Copy .env if not exists
    if [ ! -f .env ]; then
        cp .env.example .env
        print_success "Created .env file"
    else
        print_info ".env file already exists"
    fi

    # Install Composer dependencies
    if command -v composer &> /dev/null; then
        print_step "Installing Composer dependencies..."
        composer install --no-interaction --prefer-dist
        print_success "Composer dependencies installed"
    else
        print_info "Composer not found. Will install dependencies via Docker."
    fi

    cd ..
}

# Setup frontend
setup_frontend() {
    print_step "Setting up Vue.js frontend..."

    cd frontend

    # Copy .env if not exists
    if [ ! -f .env ]; then
        cp .env.example .env
        print_success "Created .env file"
    else
        print_info ".env file already exists"
    fi

    # Install npm dependencies
    print_step "Installing npm dependencies..."
    npm install
    print_success "npm dependencies installed"

    cd ..
}

# Start Docker containers
start_docker() {
    print_step "Starting Docker containers..."

    docker-compose up -d

    print_success "Docker containers started"
    print_info "Waiting for database to be ready..."
    sleep 10
}

# Initialize database
init_database() {
    print_step "Initializing database..."

    # Generate app key
    docker-compose exec -T backend php artisan key:generate
    print_success "Application key generated"

    # Run migrations
    docker-compose exec -T backend php artisan migrate --force
    print_success "Database migrations completed"

    # Seed database with demo data
    docker-compose exec -T backend php artisan db:seed --force
    print_success "Database seeded with demo data"
}

# Build frontend
build_frontend() {
    print_step "Building frontend for production..."

    cd frontend
    npm run build
    print_success "Frontend built successfully"
    cd ..
}

# Display completion message
show_completion() {
    print_header "🎉 Installation Complete!"

    echo -e "${GREEN}Compteo TN is now ready!${NC}\n"

    echo "📋 Services:"
    echo "  • Frontend:  http://localhost:3000"
    echo "  • Backend:   http://localhost:8000"
    echo "  • Mailhog:   http://localhost:8025"
    echo "  • Database:  PostgreSQL on port 5432"
    echo "  • Redis:     Redis on port 6379"

    echo -e "\n🔐 Demo Accounts:"
    echo "  Admin:      admin@compteo.tn / demo"
    echo "  DAF:        daf@compteo.tn / demo"
    echo "  Comptable:  comptable@compteo.tn / demo"
    echo "  Manager:    manager@compteo.tn / demo"
    echo "  Employé:    demo@compteo.tn / demo"

    echo -e "\n📚 Useful Commands:"
    echo "  • Start:         docker-compose up -d"
    echo "  • Stop:          docker-compose down"
    echo "  • View logs:     docker-compose logs -f"
    echo "  • Reset data:    docker-compose exec backend php artisan migrate:fresh --seed"
    echo "  • Run tests:     docker-compose exec backend php artisan test"

    echo -e "\n📖 Documentation:"
    echo "  • User Guide:        GUIDE_UTILISATEUR.md"
    echo "  • Testing Checklist: TESTING_CHECKLIST.md"
    echo "  • Deployment Guide:  docs/DEPLOYMENT.md"

    echo -e "\n${YELLOW}⚠️  Next Steps:${NC}"
    echo "  1. Review and update .env files with your configuration"
    echo "  2. Configure email settings for production"
    echo "  3. Set up SSL certificates for HTTPS"
    echo "  4. Review security settings before deploying to production"

    echo -e "\n${GREEN}Happy expense tracking! 🚀${NC}\n"
}

# Main installation flow
main() {
    print_header "Compteo TN - Installation"

    # Prompt for installation type
    echo "Select installation type:"
    echo "  1) Development (with Docker)"
    echo "  2) Development (local)"
    echo "  3) Production"
    echo -n "Enter your choice [1-3]: "
    read -r INSTALL_TYPE

    case $INSTALL_TYPE in
        1)
            print_header "Development Installation (Docker)"
            check_docker
            check_node
            setup_backend
            setup_frontend
            start_docker
            init_database
            show_completion
            ;;
        2)
            print_header "Development Installation (Local)"
            check_node

            print_info "You'll need to manually set up:"
            echo "  • PostgreSQL 16"
            echo "  • Redis 7"
            echo "  • PHP 8.3+ with required extensions"
            echo ""

            setup_backend
            setup_frontend

            print_info "Run migrations: cd backend && php artisan migrate --seed"
            print_info "Start backend:  cd backend && php artisan serve"
            print_info "Start frontend: cd frontend && npm run dev"
            ;;
        3)
            print_header "Production Installation"
            check_docker
            check_node

            setup_backend
            setup_frontend
            build_frontend

            print_header "Production Setup Complete"
            print_info "Next steps:"
            echo "  1. Review docs/DEPLOYMENT.md for detailed deployment instructions"
            echo "  2. Configure production .env files"
            echo "  3. Set up SSL certificates"
            echo "  4. Configure nginx reverse proxy"
            echo "  5. Set up automated backups"
            echo "  6. Configure monitoring and logging"
            ;;
        *)
            print_error "Invalid choice. Exiting."
            exit 1
            ;;
    esac
}

# Run main function
main
