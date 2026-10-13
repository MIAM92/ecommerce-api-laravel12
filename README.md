# API de E-commerce Segura con Swagger Completo

Este proyecto es una API RESTful desarrollada con **Laravel 12** y **PHP 8.2+** para la gestión fundamental de una tienda en línea. Implementa autenticación robusta por JWT, procesamiento seguro de pagos con Stripe y documentación OpenAPI.

## 🚀 Requisitos Técnicos Implementados
- **Framework:** Laravel 12
- **Base de Datos:** MySQL (tablas `users`, `products`, `orders`, `order_items`, `payments`)
- **Autenticación:** JWT (JSON Web Tokens)
- **Documentación:** Swagger UI (OpenAPI 3.0.0)
- **Pasarela de Pagos:** Integración oficial con Stripe API

## 🛠️ Instrucciones de Instalación y Configuración Local

1. **Clonar el repositorio:**
    ```bash
    git clone <URL_DE_TU_REPOSITORIO_GITHUB>
    cd ecommerce-api
    ```

2. **Instalar dependencias de Composer:**
    ```bash
    composer install --ignore-platform-reqs
    ```

3. **Configurar el entorno:**
    Copia el archivo de ejemplo y configura tus credenciales locales en el archivo `.env`:
    ```bash
    cp .env.example .env
    ```
    Asegúrate de llenar las credenciales de tu base de datos y Stripe:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=commerce_db
    DB_USERNAME=root
    DB_PASSWORD=

    STRIPE_KEY=tu_public_key_de_prueba
    STRIPE_SECRET=tu_secret_key_de_prueba
    ```

4. **Generar la clave de la aplicación y el secreto JWT:**
    ```bash
    php artisan key:generate
    php artisan jwt:secret
    ```

5. **Ejecutar Migraciones y Poblar la Base de Datos (Seeders):**
    ```bash
    php artisan migrate --seed
    ```

6. **Levantar el Servidor de Desarrollo:**
    ```bash
    php artisan serve
    ```

## 📄 Acceso a la Documentación de la API
Una vez encendido el servidor, puedes ingresar a la interfaz interactiva de Swagger UI para probar todos los endpoints disponibles (Registro, Login, CRUD de Productos, Procesamiento de Pagos y Consulta de Historial) en la siguiente ruta:

👉 **[http://127.0.0](http://127.0.0)**
