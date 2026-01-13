# =============================================================================
# PHP Todo Application - Production Dockerfile
# =============================================================================
# Multi-stage build for optimized production image
# Based on official PHP-FPM with Nginx

FROM php:8.3-fpm-alpine AS base

# Labels for container metadata (OCI standard)
LABEL org.opencontainers.image.title="PHP Todo App" \
      org.opencontainers.image.description="Containerized PHP Todo Application with Symfony and Doctrine" \
      org.opencontainers.image.vendor="Bauer Group" \
      org.opencontainers.image.source="https://github.com/bauer-group/php-todo-app" \
      org.opencontainers.image.licenses="MIT"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    tini \
    curl \
    icu-libs \
    libzip \
    mysql-client \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    linux-headers \
    && docker-php-ext-install \
    intl \
    opcache \
    pdo_mysql \
    zip \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apk del .build-deps \
    && rm -rf /tmp/*

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-custom.ini"
COPY docker/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create app user and directories
RUN addgroup -g 1000 app \
    && adduser -u 1000 -G app -s /bin/sh -D app \
    && mkdir -p /app/var/cache /app/var/log /run/nginx \
    && chown -R app:app /app /run/nginx /var/log/nginx /var/lib/nginx

WORKDIR /app

# =============================================================================
# Composer stage - Install dependencies
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock* symfony.lock* ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-progress \
    --no-interaction

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative \
    && composer run-script post-install-cmd --no-interaction || true

# =============================================================================
# Production stage
# =============================================================================
FROM base AS production

# Copy application code
COPY --from=composer --chown=app:app /app /app

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Pre-warm cache (optional, reduces first-request latency)
RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup --no-interaction || true

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -sf http://localhost/health || exit 1

# Expose port
EXPOSE 80

# Use tini as init system
ENTRYPOINT ["/sbin/tini", "--"]

# Start application
CMD ["/usr/local/bin/entrypoint.sh"]

# =============================================================================
# Development stage
# =============================================================================
FROM base AS development

# Install development tools
RUN apk add --no-cache \
    git \
    bash \
    vim \
    && rm -rf /var/cache/apk/*

# Install Xdebug for debugging
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug-3.4.2 \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps \
    && rm -rf /tmp/*

# Configure PHP for development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-custom.ini"
COPY docker/xdebug.ini "$PHP_INI_DIR/conf.d/xdebug.ini"

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/dev-entrypoint.sh

# Set user
USER app

# Expose ports (HTTP + Xdebug)
EXPOSE 80 9003

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["/usr/local/bin/dev-entrypoint.sh"]
