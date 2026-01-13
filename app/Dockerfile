# =============================================================================
# PHP Todo Application - Production Dockerfile
# =============================================================================
# Multi-stage build for optimized production image
# Based on official PHP-FPM with Nginx

FROM php:8.5-fpm-alpine AS base

# =============================================================================
# Metadata Labels
# =============================================================================

# Metadata
LABEL vendor="BAUER GROUP"
LABEL maintainer="Karl Bauer <karl.bauer@bauer-group.com>"

# Opencontainers Metadata
LABEL org.opencontainers.image.title="PHP Todo App [BAUER GROUP]"
LABEL org.opencontainers.image.licenses="MIT"
LABEL org.opencontainers.image.vendor="BAUER GROUP"
LABEL org.opencontainers.image.authors="Karl Bauer <karl.bauer@bauer-group.com>"
LABEL org.opencontainers.image.source="https://github.com/bauer-group/DEMO-PHP-Dockerized"
LABEL org.opencontainers.image.url="https://github.com/bauer-group/DEMO-PHP-Dockerized"
LABEL org.opencontainers.image.documentation="https://github.com/bauer-group/DEMO-PHP-Dockerized#readme"
LABEL org.opencontainers.image.description="Containerized PHP Todo Application with Symfony and Doctrine"
LABEL org.opencontainers.image.version="0.3.2"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    tini \
    curl \
    icu-libs \
    libzip \
    mysql-client \
    su-exec \
    && rm -rf /var/cache/apk/*

# Install PHP extensions using install-php-extensions (better PHP 8.5 support)
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    intl \
    opcache \
    pdo_mysql \
    zip \
    @composer

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

COPY composer.json composer.lock* ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-progress \
    --no-interaction

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative

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

# Install Xdebug 3.5.0 from source (PHP 8.5 support)
RUN apk add --no-cache --virtual .xdebug-deps $PHPIZE_DEPS linux-headers \
    && git clone --depth 1 --branch 3.5.0 https://github.com/xdebug/xdebug.git /tmp/xdebug \
    && cd /tmp/xdebug \
    && phpize \
    && ./configure --enable-xdebug \
    && make \
    && make install \
    && cd / \
    && apk del .xdebug-deps \
    && rm -rf /tmp/*

# Configure PHP for development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-custom.ini"
COPY docker/xdebug.ini "$PHP_INI_DIR/conf.d/xdebug.ini"

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/dev-entrypoint.sh

# NOTE: Runs as root like production - entrypoint handles user switching via su-exec

# Expose ports (HTTP)
EXPOSE 80

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["/usr/local/bin/dev-entrypoint.sh"]
