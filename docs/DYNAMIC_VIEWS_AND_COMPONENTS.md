# Vistas dinámicas y Sistema de Componentes

La capa de presentación de este proyecto se basa en una arquitectura de **Vistas Dirigidas por Datos (Data-Driven Views)**. En lugar de construir archivos HTML estáticos para cada sección, el sistema utiliza componentes de Blade altamente parametrizados que se adaptan según la configuración enviada desde el controlador.

## 1. Filosofía de Construcción
El objetivo principal es la **reutilización máxima**. Un solo componente de UI (como un formulario o una tabla) debe ser capaz de representar diferentes entidades de negocio (Médicos, Pacientes, etcétera) simplemente cambiando los parámetros de entrada.

### Ventajas del Modelo Parametrizado:
- **Consistencia Visual:** Todos los módulos mantienen el mismo diseño y comportamiento.
- **Mantenimiento Centralizado:** Un cambio en el componente base se refleja instantáneamente en toda la plataforma.
- **Reducción de Código:** Los archivos de vista finales son mínimos, delegando la estructura a los componentes y la lógica de datos al controlador.

---

## 2. Componentes Nucleares

### A. Formularios Dinámicos
Los formularios no se definen campo por campo en el HTML, sino mediante un **esquema de campos** (array de configuración).

- **Inyección de Datos:** El componente recibe un objeto de datos y un mapa de campos. Automáticamente vincula los valores existentes (útil para edición) o inicializa valores vacíos (creación).
- **Lógica Condicional:** Mediante atributos de condición, el componente puede decidir qué campos mostrar u ocultar en el cliente (usando Alpine.js) basándose en roles o selecciones previas, sin necesidad de recargar la página.
- **Abstracción de Inputs:** Utiliza sub-componentes dinámicos para renderizar el tipo de input correcto (text, select, checkbox, multi-select) basándose únicamente en el parámetro `type`.

### B. Tablas de Datos Flexibles
Para la visualización de registros, se utiliza un sistema de tablas abstractas que separa la estructura de la información.

- **Definición de Columnas:** Se pasa un array que define el encabezado, el campo de la base de datos a mostrar y clases de estilo específicas.
- **Renderizado Adaptativo:** El componente soporta tres formas de visualización por celda:
    1. **Valor Directo:** Muestra el dato crudo de la base de datos.
    2. **Callbacks de Renderizado:** Permite formatear el dato mediante lógica PHP antes de mostrarlo.
    3. **Componentes Personalizados:** Permite insertar otros componentes (como badges de estado o botones de acción) dentro de una celda específica usando `<x-dynamic-component>`.

---

## 3. El Proceso de Construcción de una Vista

El flujo para renderizar una interfaz sigue estos pasos:

1. **Definición en Controlador:** El controlador prepara un "Esquema" (array con la configuración de la UI) y los "Datos" (basado en los modelos de Eloquent).
2. **Paso de Props:** Se invocan los componentes en la vista de Blade pasando estas variables a través de la directiva `@props`.
3. **Mapeo y Evaluación:**
    - El componente **mapea** los datos a los campos/columnas correspondientes.
    - Se evalúan las **condiciones de estado** (ej. si el usuario tiene rol 'DOCTOR').
4. **Hidratación en Cliente:** Se exportan las configuraciones necesarias hacia **Alpine.js** para manejar la interactividad local (modales, validaciones en tiempo real, toggles de visibilidad).

---

## 4. Ejemplo de Flujo de Trabajo

Para agregar una nueva sección (ej. Gestión de Especialidades):
1. **No se crea un HTML nuevo desde cero.**
2. Se define el array de columnas para la tabla en el archivo en que se lo invoca.
3. Se define el array de campos para el formulario en el archivo en que se lo necesita.

Esto garantiza que la lógica de "cómo se ve una tabla" o "cómo se comporta un formulario" esté desacoplada de "qué datos contiene".