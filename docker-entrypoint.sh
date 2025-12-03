#!/bin/bash
set -e

echo "🚀 Iniciando AtentiDoc..."

# Esperar a que la base de datos esté lista
echo "⏳ Esperando conexión a base de datos..."
sleep 5

# Crear directorios necesarios si no existen
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Limpiar cachés antiguos
echo "🧹 Limpiando cachés..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️  Error en migraciones (continuando...)"

# Cachear configuraciones para producción
echo "⚡ Optimizando configuraciones..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Aplicación lista!"

# Iniciar Apache (necesitamos volver a root temporalmente)
exec apache2-foreground