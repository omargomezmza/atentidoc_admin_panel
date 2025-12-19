# Arquitectura del Backend

## 1. Rutas (`routes/web.php`)
Las rutas están agrupadas bajo middlewares de autenticación. 

## 2. Controllers (`app/Http/Controllers`)
Los controladores actúan como directores de orquesta. **No contienen lógica de negocio ni de validación pesada.**
- Reciben el `Request` inyectado.
- Llaman al `Service` correspondiente.
- Retornan una `View` con los datos o una redirección.

## 3. Form Requests (`app/Http/Requests`)
Encargados de la validación. Implementan reglas complejas como:
- Unicidad en tablas específicas (`unique:users,email`).
- Campos condicionales basados en el rol (ej: `license_number` requerido solo para `DOCTOR`).
- Complejidad de contraseñas.

## 4. Servicios (`app/Services`)
Clases puras de PHP donde reside la lógica de negocio.
- Interactúan con los Modelos.
- Realizan las operaciones en la base de datos.
- Son llamados por los controladores para mantener el principio de responsabilidad única.