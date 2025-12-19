# Modelos y Base de Datos

El sistema utiliza modelos de Eloquent que apuntan a tablas preexistentes. Es fundamental respetar la estructura de nombres y claves foráneas para mantener la compatibilidad con el backend Java.

## Modelos Principales

### User (`App\Models\User`)
Es el modelo central de autenticación.
- **Tabla:** `users`
- **Relaciones:** - `roles()`: HasMany (UserRole)
    - `doctor()`: HasOne (Doctor)
    - `patient()`: HasOne (Patient)

### Doctor (`App\Models\Doctor`)
Almacena información profesional.
- **Tabla:** `doctors`
- **Clave Foránea:** `user_id`

### Patient (`App\Models\Patient`)
Almacena información clínica y de cobertura.
- **Tabla:** `patients`
- **Clave Foránea:** `user_id`

## Consideración de Hashing
Para asegurar la compatibilidad con Spring Boot, se comprobó mediante pruebas manuales que los hashes de contraseñas generados por Laravel (`$2y$`), a pesar de presentar un formato diferente a los generados por Spring Boot (`$2a$`), son interoperables y permiten a ambos sistemas verificar las contraseñas de los usuarios que inician sesión.