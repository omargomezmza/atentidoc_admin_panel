# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# ===== INSTALAR DEPENDENCIAS DEL SISTEMA =====
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ===== INSTALAR NODE.JS Y PNPM =====
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g pnpm@latest

# ===== INSTALAR COMPOSER =====
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ===== ESTABLECER DIRECTORIO DE TRABAJO =====
WORKDIR /var/www/html

# ===== DEPENDENCIAS DE NODE.JS =====
# Copiar package.json y lockfile
COPY package.json pnpm-lock.yaml ./

# Copiar archivos de configuración si existen (usando wildcards)
COPY vite.config.* ./
COPY postcss.config.* ./
COPY tailwind.config.* ./

# Instalar dependencias de Node
RUN pnpm install --frozen-lockfile

# ===== DEPENDENCIAS DE PHP =====
# Copiar composer files
COPY composer.json composer.lock ./

# Instalar sin ejecutar scripts
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# ===== COPIAR TODO EL CÓDIGO =====
COPY . /var/www/html

# ===== EJECUTAR SCRIPTS DE COMPOSER =====
RUN composer run-script post-autoload-dump --no-interaction

# ===== COMPILAR ASSETS =====
RUN pnpm build

# ===== LIMPIAR DEPENDENCIAS DE DESARROLLO =====
RUN pnpm prune --prod

# ===== CREAR DIRECTORIOS Y PERMISOS =====
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache

# ===== CONFIGURAR APACHE =====
RUN a2enmod rewrite
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n\
\n\
ServerName localhost' >> /etc/apache2/apache2.conf

# ===== EXPONER PUERTO =====
EXPOSE 80

# ===== SCRIPT DE INICIO =====
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ===== COMANDO DE INICIO =====
CMD ["docker-entrypoint.sh"]