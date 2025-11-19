#!/bin/bash

###############################################################################
# Compteo TN - Automated Backup Script
# Creates backups of database, files, and configuration
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
S3_BUCKET="s3://your-bucket/compteo-backups"  # Optional: S3 storage
RETENTION_DAYS=30
DATE=$(date +%Y%m%d_%H%M%S)
DAY=$(date +%A)

print_success() { echo -e "${GREEN}✓${NC} $1"; }
print_error() { echo -e "${RED}✗${NC} $1"; }
print_info() { echo -e "${YELLOW}ℹ${NC} $1"; }
print_step() { echo -e "${BLUE}➤${NC} $1"; }

# Create backup directory
create_backup_dir() {
    mkdir -p "$BACKUP_DIR"/{daily,weekly,monthly}
    print_success "Backup directories created"
}

# Backup database
backup_database() {
    print_step "Backing up PostgreSQL database..."

    DB_BACKUP_FILE="$BACKUP_DIR/daily/db_$DATE.sql.gz"

    # Dump database
    docker-compose exec -T postgres pg_dump \
        -U compteo \
        -d compteo_tunisia \
        --clean \
        --if-exists \
        | gzip > "$DB_BACKUP_FILE"

    print_success "Database backed up: $(du -h "$DB_BACKUP_FILE" | cut -f1)"
}

# Backup Redis data
backup_redis() {
    print_step "Backing up Redis data..."

    REDIS_BACKUP_FILE="$BACKUP_DIR/daily/redis_$DATE.rdb"

    # Save Redis snapshot
    docker-compose exec -T redis redis-cli --no-auth-warning -a compteo_redis_2025 SAVE
    docker cp compteo-redis:/data/dump.rdb "$REDIS_BACKUP_FILE"

    print_success "Redis backed up: $(du -h "$REDIS_BACKUP_FILE" | cut -f1)"
}

# Backup application files
backup_files() {
    print_step "Backing up application files..."

    FILES_BACKUP="$BACKUP_DIR/daily/storage_$DATE.tar.gz"

    tar -czf "$FILES_BACKUP" \
        -C "$APP_DIR/backend" \
        storage/app \
        storage/logs \
        2>/dev/null || true

    print_success "Files backed up: $(du -h "$FILES_BACKUP" | cut -f1)"
}

# Backup environment files
backup_env() {
    print_step "Backing up environment configuration..."

    ENV_BACKUP="$BACKUP_DIR/daily/env_$DATE.tar.gz"

    tar -czf "$ENV_BACKUP" \
        "$APP_DIR/backend/.env" \
        "$APP_DIR/frontend/.env" \
        "$APP_DIR/docker-compose.yml" \
        2>/dev/null || true

    # Encrypt sensitive data
    if command -v gpg &> /dev/null; then
        gpg --symmetric --cipher-algo AES256 "$ENV_BACKUP"
        rm "$ENV_BACKUP"
        print_success "Environment backed up and encrypted"
    else
        print_success "Environment backed up (not encrypted)"
    fi
}

# Create weekly backup
create_weekly_backup() {
    if [ "$DAY" = "Sunday" ]; then
        print_step "Creating weekly backup..."

        WEEKLY_BACKUP="$BACKUP_DIR/weekly/full_backup_$(date +%Y_week%V).tar.gz"

        tar -czf "$WEEKLY_BACKUP" \
            "$BACKUP_DIR/daily/db_$DATE.sql.gz" \
            "$BACKUP_DIR/daily/redis_$DATE.rdb" \
            "$BACKUP_DIR/daily/storage_$DATE.tar.gz" \
            2>/dev/null || true

        print_success "Weekly backup created: $(du -h "$WEEKLY_BACKUP" | cut -f1)"
    fi
}

# Create monthly backup
create_monthly_backup() {
    if [ "$(date +%d)" = "01" ]; then
        print_step "Creating monthly backup..."

        MONTHLY_BACKUP="$BACKUP_DIR/monthly/full_backup_$(date +%Y_%m).tar.gz"

        tar -czf "$MONTHLY_BACKUP" \
            "$BACKUP_DIR/daily/db_$DATE.sql.gz" \
            "$BACKUP_DIR/daily/redis_$DATE.rdb" \
            "$BACKUP_DIR/daily/storage_$DATE.tar.gz" \
            2>/dev/null || true

        print_success "Monthly backup created: $(du -h "$MONTHLY_BACKUP" | cut -f1)"
    fi
}

# Cleanup old backups
cleanup_old_backups() {
    print_step "Cleaning up old backups..."

    # Remove daily backups older than RETENTION_DAYS
    find "$BACKUP_DIR/daily" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
    find "$BACKUP_DIR/daily" -name "*.rdb" -mtime +$RETENTION_DAYS -delete
    find "$BACKUP_DIR/daily" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete

    # Keep last 4 weekly backups
    ls -t "$BACKUP_DIR/weekly"/*.tar.gz 2>/dev/null | tail -n +5 | xargs -r rm

    # Keep last 12 monthly backups
    ls -t "$BACKUP_DIR/monthly"/*.tar.gz 2>/dev/null | tail -n +13 | xargs -r rm

    print_success "Old backups cleaned up"
}

# Upload to S3 (optional)
upload_to_s3() {
    if command -v aws &> /dev/null && [ -n "$S3_BUCKET" ]; then
        print_step "Uploading to S3..."

        aws s3 sync "$BACKUP_DIR" "$S3_BUCKET" \
            --storage-class STANDARD_IA \
            --exclude "*" \
            --include "daily/*$DATE*" \
            --include "weekly/*" \
            --include "monthly/*"

        print_success "Backups uploaded to S3"
    else
        print_info "S3 upload skipped (aws CLI not configured)"
    fi
}

# Verify backups
verify_backups() {
    print_step "Verifying backups..."

    # Check if database backup is valid
    if gzip -t "$BACKUP_DIR/daily/db_$DATE.sql.gz" 2>/dev/null; then
        print_success "Database backup is valid"
    else
        print_error "Database backup is corrupted!"
        exit 1
    fi

    # Check if files exist
    if [ -f "$BACKUP_DIR/daily/storage_$DATE.tar.gz" ]; then
        print_success "File backup exists"
    else
        print_error "File backup is missing!"
        exit 1
    fi
}

# Generate backup report
generate_report() {
    REPORT_FILE="$BACKUP_DIR/backup_report_$DATE.txt"

    cat > "$REPORT_FILE" <<EOF
Compteo TN - Backup Report
==========================

Date: $(date)
Server: $(hostname)

Backup Files:
-------------
$(ls -lh "$BACKUP_DIR/daily" | grep "$DATE")

Disk Usage:
-----------
Total backup size: $(du -sh "$BACKUP_DIR" | cut -f1)

Database: $(du -h "$BACKUP_DIR/daily/db_$DATE.sql.gz" 2>/dev/null | cut -f1)
Redis: $(du -h "$BACKUP_DIR/daily/redis_$DATE.rdb" 2>/dev/null | cut -f1)
Files: $(du -h "$BACKUP_DIR/daily/storage_$DATE.tar.gz" 2>/dev/null | cut -f1)

Retention Policy:
-----------------
Daily backups: $RETENTION_DAYS days
Weekly backups: 4 weeks
Monthly backups: 12 months

Status: ✓ SUCCESS
EOF

    print_success "Backup report generated: $REPORT_FILE"
}

# Send notification
send_notification() {
    # Optional: Send email notification
    # mail -s "Compteo TN Backup Success" admin@compteo.tn < "$REPORT_FILE"

    # Optional: Send to Slack
    # curl -X POST -H 'Content-type: application/json' \
    #     --data "{\"text\":\"Compteo TN backup completed successfully\"}" \
    #     YOUR_SLACK_WEBHOOK_URL

    print_success "Backup completed successfully!"
}

# Main backup flow
main() {
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}  Compteo TN - Backup Script${NC}"
    echo -e "${BLUE}  $(date)${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

    create_backup_dir
    backup_database
    backup_redis
    backup_files
    backup_env
    create_weekly_backup
    create_monthly_backup
    verify_backups
    cleanup_old_backups
    upload_to_s3
    generate_report
    send_notification
}

# Run main function
main
