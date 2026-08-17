# ==============================================================================
# 🌙 macatung.dev — Multi-Stage Production Dockerfile
# Stage 1: Build Frontend Assets (Vite + Vue 3 + TailwindCSS)
# Stage 2: Install PHP Composer Dependencies
# Stage 3: Production Alpine Runtime with PHP 8.2-FPM, Nginx & Supervisord
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Frontend Asset Builder
# ------------------------------------------------------------------------------
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Install npm dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy frontend source files
COPY tsconfig.json tsconfig.app.json tsconfig.node.json vite.config.ts postcss.config.js tailwind.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
COPY src/ ./src/
COPY index.html ./

# Build production bundles to public/build
RUN npm run build

# ------------------------------------------------------------------------------
# Stage 2: Composer Dependencies Builder
# ------------------------------------------------------------------------------
FROM composer:2 AS composer-builder
WORKDIR /app

# Copy composer definition files
COPY composer.json composer.lock ./

# Install vendor packages (no dev, ignore platform reqs for strict compatibility)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Copy application source including views for autoload optimization
COPY app/ ./app/
COPY bootstrap/ ./bootstrap/
COPY config/ ./config/
COPY database/ ./database/
COPY resources/ ./resources/
COPY routes/ ./routes/
COPY artisan ./

RUN composer dump-autoload --optimize --no-dev

# ------------------------------------------------------------------------------
# Stage 3: Final Production Runtime
# ------------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS runner

# Install Nginx, Supervisor, SQLite and required system packages
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    sqlite-libs \
    sqlite-dev \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    libxml2-dev

# Install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        zip \
        bcmath \
        opcache \
        intl \
        xml \
        ctype \
        fileinfo \
        gd

# Set working directory
WORKDIR /var/www/html

# Copy application files (code, views, vendor) from composer-builder
COPY --from=composer-builder /app /var/www/html

# Copy built frontend assets from frontend-builder
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Copy remaining project files
COPY .env.example /var/www/html/.env.example
COPY public/ /var/www/html/public/

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configure PHP settings
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure Supervisord
COPY docker/supervisord.conf /etc/supervisord.conf

# Configure Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

# Set up storage and cache permissions
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
             /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose HTTP port
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Define Entrypoint and default command
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
