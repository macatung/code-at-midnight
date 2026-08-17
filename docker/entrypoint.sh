#!/bin/sh
set -e

echo "🌙 [macatung.dev] Starting Code at midnight container initialization..."

# Remove Vite dev server hot file if present
rm -f /var/www/html/public/hot

# 1. Environment file check
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "📄 .env not found, copying from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    else
        echo "⚠️ .env.example not found, creating minimal .env..."
        cat << 'EOF' > /var/www/html/.env
APP_NAME="Macatung Portfolio"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_URL=http://localhost:8000
LOG_CHANNEL=stack
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
EOF
    fi
fi

# 2. Application Key Check & Generation
if ! grep -q "^APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generating Laravel Application Key..."
    php artisan key:generate --force
fi

# 3. SQLite Database Initialization
DB_CONN=$(grep "^DB_CONNECTION=" /var/www/html/.env 2>/dev/null | cut -d '=' -f2 || echo "sqlite")
if [ "$DB_CONN" = "sqlite" ] || [ -z "$DB_CONN" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        echo "🗄️ Initializing SQLite database file..."
        touch /var/www/html/database/database.sqlite
    fi
    chmod -R 777 /var/www/html/database
fi

# 4. Storage & Cache Directory Permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Database Migration & Seeding
if [ "$AUTO_MIGRATE" != "false" ]; then
    echo "⚡ Running database migrations and seeders..."
    php artisan migrate --force --seed || echo "⚠️ Migration warning, skipping..."
fi

# 6. Cache Optimization (if in production)
if [ "$APP_ENV" = "production" ]; then
    echo "🚀 Caching Laravel configurations for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "✨ [macatung.dev] Container ready on port 80 (mapped to host 8000)!"

# Execute passed command (default: supervisord)
exec "$@"
