#!/bin/bash

# Esperar a que la base de datos esté lista
echo "Esperando a la base de datos..."
sleep 5

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Apache
apache2-foreground