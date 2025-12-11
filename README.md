# AtentiDoc Panel Web

Panel de administración web desarrollado con Laravel para la gestión de la aplicación móvil de AtentiDoc.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Tecnologías](#tecnologías)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Ejecución en Entorno Local](#ejecución-en-entorno-local)
- [Despliegue](#despliegue)
- [Estructura de Configuración](#estructura-de-configuración)

## 🔧 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu sistema:

- **PHP 8.3.15** o superior
- **Composer 2.4.1** o superior
- **Node.js** (versión LTS recomendada)
- **pnpm** (gestor de paquetes)
- **Git**
- **MySQL** o **PostgreSQL** (u otro motor de base de datos compatible)

### Instalación de Requisitos

#### PHP
```bash
# En Ubuntu/Debian
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-curl php8.3-mbstring php8.3-zip

# En macOS (usando Homebrew)
brew install php@8.3

# En Windows, descarga desde: https://windows.php.net/download/
```

#### Composer
```bash
# Descarga e instalación global
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer

# Verifica la instalación
composer --version
```

#### pnpm
```bash
# Instalación global con npm
npm install -g pnpm

# Verifica la instalación
pnpm --version
```

## 🛠️ Tecnologías

El proyecto utiliza las siguientes tecnologías:

| Tecnología | Versión |
|-----------|---------|
| PHP | 8.3.15 |
| Composer | 2.4.1 |
| Laravel | ^12.0 |
| Alpine.js | ^3.15.2 |
| Tailwind CSS | ^4.1.17 |
| Vite | ^7.0.7 |

## 📦 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/davidsandez/atentidoc-dashboard.git
cd atentidoc-dashboard
```

### 2. Instalar Dependencias de PHP

```bash
composer install
```

### 3. Configurar el Archivo de Entorno

Crea el archivo `.env` a partir del ejemplo proporcionado:

```bash
cp .env.example .env
```

### 4. Generar la Clave de Aplicación

```bash
php artisan key:generate
```

### 5. Instalar Dependencias de Node.js

```bash
pnpm install
```

## ⚙️ Configuración

### Conexión a la Base de Datos

Edita el archivo `.env` en la raíz del proyecto y configura las siguientes variables con las credenciales de tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

**Nota:** La aplicación web utiliza una base de datos propia para gestionar la sesión de los usuarios que acceden al panel de administración.

### Conexión a la API

La aplicación define la ruta de acceso al backend desde el que obtiene sus datos en el apartado `api`, dentro del archivo `config/services.php`.

Edita este archivo para configurar la URL de tu API:

```php
'api' => [
    'base_url' => env('API_BASE_URL', 'https://api.atentidoc.com'),
    // Otras configuraciones...
],
```

Puedes definir la variable `API_BASE_URL` en tu archivo `.env`:

```env
API_BASE_URL=https://dev.atentidoc.com.ar
```

### Ejecutar Migraciones

Solo en los casos en que se esté desee apuntar a una base de datos nueva, se ejecuta el comando para realizar las migraciones:

```bash
php artisan migrate
```

## 🚀 Ejecución en Entorno Local

Para ejecutar la aplicación en tu entorno de desarrollo local, necesitas iniciar dos procesos:

### 1. Servidor de Desarrollo de Vite

En una terminal, ejecuta:

```bash
pnpm dev
```

Este comando iniciará el servidor de desarrollo de Vite para compilar los assets (CSS, JavaScript) en tiempo real.

### 2. Servidor de Laravel

En otra terminal, ejecuta:

```bash
php artisan serve
```

El servidor de Laravel estará disponible por defecto en: **http://localhost:8000**

## 🌐 Despliegue

### Variables de Entorno en Producción

Cuando despliegues la aplicación en un servidor de producción, asegúrate de configurar las siguientes variables de entorno desde el panel de administración de tu proveedor de hosting:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `API_BASE_URL`

### Compilación de Assets para Producción

Antes de desplegar, compila los assets para producción:

```bash
pnpm build
```

### Optimización de Laravel

Ejecuta los siguientes comandos para optimizar la aplicación:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 Estructura de Configuración

```
atentidoc-panel-web/
├── config/
│   └── services.php          # Configuración de la API
├── .env                       # Variables de entorno (local)
├── .env.example              # Plantilla de variables de entorno
├── composer.json             # Dependencias de PHP
├── package.json              # Dependencias de Node.js
├── vite.config.js            # Configuración de Vite
└── tailwind.config.js        # Configuración de Tailwind CSS
```

## 📝 Notas Adicionales

- Asegúrate de que el directorio `storage` y `bootstrap/cache` tengan permisos de escritura.
- Mantén el archivo `.env` fuera del control de versiones (ya está incluido en `.gitignore`).
- Para más información sobre Laravel, visita la [documentación oficial](https://laravel.com/docs).

## 🤝 Contribución

Si deseas contribuir al proyecto, por favor sigue las guías de contribución establecidas.

El repositorio usará un sistema de ramas simple, claro y escalable:

- **master** → rama de producción  
- **dev** → rama de desarrollo  
- **feat/*** → ramas por funcionalidad, se integran a *dev* mediante pull requests