FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    sqlite \
    sqlite-dev \
    curl \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create cache and database directories
RUN mkdir -p storage/framework/{cache,sessions,views} database \
    && touch database/database.sqlite \
    && chmod -R 777 storage bootstrap/cache database

# Copy Nginx config
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 8080;
    server_name _;
    root /app/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# Copy Supervisor config
COPY <<EOF /etc/supervisor.d/laravel.ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=1
stderr_logfile=/var/log/laravel-worker.err.log
stdout_logfile=/var/log/laravel-worker.out.log

[program:laravel-scheduler]
process_name=%(program_name)s
command=php /app/artisan schedule:work
autostart=true
autorestart=true
stderr_logfile=/var/log/laravel-scheduler.err.log
stdout_logfile=/var/log/laravel-scheduler.out.log
EOF

# Generate app key
RUN php artisan key:generate --no-interaction || true

# Expose port
EXPOSE 8080

# Start services
CMD ["sh", "-c", "sed -i \"s/listen 8080;/listen ${PORT:-8080};/g\" /etc/nginx/http.d/default.conf && mkdir -p /app/database && touch /app/database/database.sqlite && chmod -R 777 /app/database /app/storage /app/bootstrap/cache && php artisan migrate --force && php-fpm -D && supervisord -c /etc/supervisord.conf && nginx -g 'daemon off;'"]
