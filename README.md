# 🛠️ Sistema de Gestión de Mantenimiento AIP

Plataforma web moderna para el registro, organización y seguimiento del ciclo de vida de los equipos tecnológicos (computadoras, servidores, laptops) y componentes en Aulas de Innovación Pedagógica (AIP). 

Construido con **Laravel 12**, **TailwindCSS 4**, **Alpine.js** e integrado con la API de **Groq (LLaMA)** para diagnósticos y mantenimiento predictivo asistido por Inteligencia Artificial.

---

## 🌟 Características Principales

*   **👥 Control de Acceso (RBAC):** Roles definidos para **Administrador** (gestión total de inventario y usuarios), **Técnico** (registro de órdenes de trabajo, fotos y diagnósticos por IA) y **Supervisor** (acceso a reportes ejecutivos).
*   **🤖 Inteligencia Artificial (Groq API):**
    *   *Diagnóstico Inteligente:* Sugerencias automáticas de solución y repuestos según la descripción de la falla.
    *   *Análisis Predictivo:* Proyección de fallas futuras y estimación de vida útil basándose en el historial del equipo.
*   **📄 Reportes en PDF (DomPDF):** Exportación de Fichas de Bienes, Reportes Técnicos, Inversión y Actas de Baja con membrete institucional.
*   **📧 Notificaciones por Correo:** Alertas instantáneas de mantenimientos y fallas críticas enviadas automáticamente vía **Resend**.
*   **📊 Dashboard Operativo:** Métricas en tiempo real sobre el estado de la infraestructura y balance de carga de trabajo de los técnicos.

---

## 📋 Tabla de Contenidos

- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación y Configuración](#-instalación-y-configuración)
- [Ejecución en Desarrollo](#-ejecución-en-desarrollo)
- [Scripts Clave](#-scripts-clave)
- [Calidad de Código y Pruebas](#-calidad-de-código-y-pruebas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Licencia](#-licencia)

---

## 🔧 Requisitos del Sistema

*   **PHP** >= 8.2 & **Composer** >= 2.0
*   **Node.js** >= 18.0 & **npm** >= 9.0
*   **MySQL** >= 8.0 o MariaDB
*   Una API Key de **Groq** y de **Resend** (opcionales para el funcionamiento local básico).

---

## 📥 Instalación y Configuración

### 1. Clonar el repositorio y preparar el entorno
```bash
git clone https://github.com/sethner/sistema-mantenimiento.git
cd sistema-mantenimiento
cp .env.example .env
```

### 2. Configurar la base de datos y APIs externas
Crea la base de datos en MySQL:
```sql
CREATE DATABASE sistema_mantenimiento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Edita tu archivo `.env` configurando los accesos a tu base de datos y tus claves de API:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_mantenimiento
DB_USERNAME=root
DB_PASSWORD=tu_contraseña

GROQ_API_KEY=tu_groq_api_key
RESEND_API_KEY=tu_resend_api_key
```

### 3. Aprovisionar el proyecto
Elige **una** de las siguientes opciones para instalar dependencias y migrar la base de datos:

#### Opción A: Instalación Rápida (Recomendada)
Usa el comando automatizado para instalar paquetes (PHP y Node), generar la clave del sistema y compilar los recursos de frontend:
```bash
composer setup
php artisan db:seed
```

#### Opción B: Instalación Manual
Si deseas ejecutar cada paso de manera independiente:
```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

*Las credenciales de acceso inicial de prueba creadas son:*
*   **Administrador:** `admin@gmail.com` / `12345678`
*   **Técnico:** `tecnico@gmail.com` / `12345678`
*   **Supervisor:** `supervisor@gmail.com` / `12345678`

### 4. Crear enlace simbólico de archivos
Permite visualizar correctamente las imágenes subidas como evidencias de mantenimientos:
```bash
php artisan storage:link
```

---

## 🚀 Ejecución en Desarrollo

Puedes arrancar la aplicación de dos formas:

### Opción A: Servidor Concurrente (Recomendado)
Inicia de forma simultánea el servidor web de Laravel, el compilador Vite, el lector de logs Pail y el procesador de colas en una sola consola:
```bash
composer dev
```
Accede al sistema desde: **[http://localhost:8000](http://localhost:8000)**

### Opción B: Servidores Individuales
Ejecuta cada proceso por separado si necesitas depurar de forma aislada:
```bash
# Servidor web local
php artisan serve

# Compilador de assets (TailwindCSS 4)
npm run dev

# Procesador de colas para correos y notificaciones
php artisan queue:listen
```

---

## 📜 Scripts Clave

| Comando | Tipo | Descripción |
| :--- | :--- | :--- |
| `composer setup` | Composer | Instalación limpia inicial y compilación del entorno. |
| `composer dev` | Composer | Levanta todos los servidores necesarios para desarrollo. |
| `composer test` | Composer | Limpia caché del framework y corre las pruebas del sistema. |
| `npm run dev` | NPM | Compila assets en segundo plano con Hot-Reload. |
| `npm run build` | NPM | Compila y optimiza los recursos de Tailwind 4 para producción. |

---

## 🧪 Calidad de Código y Pruebas

### Pruebas Unitarias y Funcionales (Testing)
El sistema incluye pruebas automatizadas para validar flujos de mantenimiento, notificaciones e IA:
```bash
# Correr suite completa
composer test

# Correr una prueba específica
php artisan test --filter=test_mantenimiento_attention_persists_diagnosis_action_and_component_state
```

### Laravel Pint (Estándar de Código PSR-12)
Para mantener un estilo uniforme en toda la lógica de PHP:
```bash
# Buscar inconsistencias de estilo
./vendor/bin/pint --test

# Corregir y formatear automáticamente
./vendor/bin/pint
```

---

## 📁 Estructura del Proyecto

Distribución simplificada del código fuente principal:

```
sistema-mantenimiento/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controladores (Equipos, Mantenimientos, PDF, IA)
│   │   ├── Middleware/       # Middleware para control de roles
│   │   └── Requests/         # Validaciones de formularios
│   ├── Models/               # Modelos de base de datos (Equipo, Componente, Mantenimiento)
│   ├── Notifications/        # Notificaciones de estado e integraciones de correo
│   └── Services/             # Conexión directa y prompts de la API de Groq
├── database/
│   ├── migrations/           # Definición del esquema de la base de datos
│   └── seeders/              # Datos semilla para roles y usuarios iniciales
├── resources/
│   ├── css/                  # Estilos base con TailwindCSS 4
│   ├── js/                   # Interactividad con Alpine.js
│   └── views/                # Vistas de Laravel Blade por módulos
├── routes/
│   ├── web.php               # Rutas de la plataforma web
│   └── auth.php              # Rutas de autenticación (Breeze)
└── tests/                    # Pruebas automatizadas (Unitarias y Funcionales)
```

---

## 📄 Licencia

Este proyecto está licenciado bajo la **Licencia MIT**. Consulta el archivo `LICENSE` para más detalles.
