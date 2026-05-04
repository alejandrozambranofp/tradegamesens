# TradeGameSense

Bienvenido a **TradeGameSense**, la plataforma definitiva diseñada para que la comunidad gamer comparta, descubra y valore guías, trucos y estrategias de sus videojuegos favoritos. 

Este proyecto ha sido desarrollado como una Single Page Application (SPA) robusta y moderna, enfocada en ofrecer la mejor experiencia de usuario con una interfaz limpia, oscura y orientada al gaming.

---

## Características Principales

### Para la Comunidad (Usuarios)
* **Exploración de Guías**: Buscador avanzado integrado en la página principal con filtros instantáneos por juego y categoría.
* **Creación de Contenido**: Un editor de texto enriquecido que permite a los usuarios redactar y maquetar sus propias guías de forma intuitiva, incluyendo la subida de imágenes y herramientas de formato.
* **Sistema de Valoraciones y Comentarios**: Los usuarios pueden puntuar de 1 a 5 estrellas y dejar comentarios en las guías de otros jugadores para fomentar el debate y la calidad.
* **Gestión de Perfil**: Panel personalizado para administrar las guías publicadas, cambiar la imagen de avatar y gestionar la lista de guías guardadas en favoritos.
* **Colecciones**: Agrupación visual de guías organizadas por franquicias o juegos específicos.

### Para la Administración
* **Panel de Control (Dashboard)**: Interfaz dedicada para supervisar las métricas y estadísticas principales de la plataforma (visitas, usuarios activos, guías totales).
* **Gestión Integral**: Módulos completos (CRUD) para administrar Usuarios, Roles, Juegos, Categorías y revisar todas las Guías publicadas en la plataforma.

---

## Tecnologías Utilizadas

El proyecto está construido sobre una pila tecnológica moderna que garantiza escalabilidad y un alto rendimiento:

* **Backend**: Laravel 10 (PHP 8.1+) - Arquitectura API RESTful, autenticación de sesiones y tokens con Laravel Sanctum y gestión de roles/permisos con Spatie.
* **Frontend**: Vue 3 (Composition API) - Gestión del estado global con Pinia y enrutamiento dinámico con Vue Router.
* **Diseño e Interfaz**: Tailwind CSS para el maquetado a medida y responsivo, combinado con PrimeVue como biblioteca de componentes avanzados de interfaz de usuario.
* **Base de Datos**: MySQL / MariaDB.

---

## Guía de Instalación y Despliegue Local

Sigue estos pasos para ejecutar TradeGameSense en tu propio entorno de desarrollo:

### 1. Requisitos Previos
* PHP >= 8.1
* Composer
* Node.js >= 16 y npm
* MySQL o MariaDB

### 2. Preparar el Backend (Laravel)
Clona el repositorio en tu máquina local:
```bash
git clone <url-del-repositorio>
cd tradegamesense
```

Instala las dependencias de PHP:
```bash
composer install
```

Configura el archivo de variables de entorno:
```bash
cp .env.example .env
php artisan key:generate
```

Abre el archivo `.env` recién creado y configura tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=root
DB_PASSWORD=
```

Ejecuta las migraciones y puebla la base de datos con la información inicial (juegos predefinidos, categorías, usuarios de prueba e imágenes):
```bash
php artisan migrate --seed
```

### 3. Preparar el Frontend (Vue) y Ejecutar
Instala las dependencias de Node.js:
```bash
npm install
```

Para arrancar el proyecto necesitarás abrir dos terminales simultáneamente:

**Terminal 1 (Servidor Backend):**
```bash
php artisan serve
```

**Terminal 2 (Compilador Frontend / Vite):**
```bash
npm run dev
```

Una vez ambos comandos estén en ejecución, la aplicación estará disponible en tu navegador accediendo a: `http://localhost:8000`.

---

## Guía de Usuario (Cómo usar la plataforma)

### 1. Registro e Inicio de Sesión
Para poder interactuar con la plataforma (publicar tus propias guías, votar o comentar) es estrictamente necesario tener una cuenta de usuario. 
* Dirígete a la sección de **Registro** situada en el menú principal superior.
* Rellena tus datos personales (nombre, apellidos) y tus credenciales de acceso.
* Una vez registrado, podrás acceder desde **Iniciar Sesión**. 
*(Nota para desarrollo: Si has ejecutado los seeders en la instalación, puedes entrar usando las credenciales de prueba como `user@demo.com` o `admin@demo.com` con la contraseña `12345678`).*

### 2. Explorar y Buscar Guías
* Desde la **Página Principal (Home)** tienes acceso directo a un buscador interactivo. Puedes teclear directamente el nombre de un juego, o utilizar los selectores desplegables para filtrar de manera precisa por el título del juego o la categoría que te interese (Ej. "Estrategia", "Easter Eggs", "Rutas").
* También puedes navegar hacia el apartado **Colecciones** para ver listados agrupados de las franquicias más populares y acceder a todas las guías de un juego en concreto con un solo clic.

### 3. Leer, Valorar y Guardar
* Al hacer clic en cualquier tarjeta de guía, entrarás en su vista de lectura detallada.
* En la parte inferior del contenido de la guía encontrarás la sección de **Comunidad**. Si tienes la sesión iniciada, podrás dejar una puntuación interactiva mediante estrellas y adjuntar un comentario escrito que servirá de feedback para el autor y el resto de lectores.
* Si la guía te resulta muy útil y quieres tenerla a mano, en la cabecera dispones de un botón de **Favoritos** para guardarla directamente en tu colección personal accesible desde tu perfil.

### 4. Publicar tu propia Guía (Contribuir)
Cualquier usuario registrado puede aportar a la comunidad creando contenido:
* Abre el menú desplegable de tu perfil (esquina superior derecha) y pulsa en **Contribuir**.
* Se abrirá el formulario de creación de guías. Sigue los siguientes pasos:
  * Selecciona y sube una **Imagen de portada** atractiva.
  * Define el **Título** de tu guía.
  * Selecciona el **Juego** al que pertenece, asigna las **Categorías** correspondientes y marca el nivel de **Dificultad**.
  * Utiliza el panel inferior para redactar. Puedes aplicar formatos como negritas, listas estructuradas e incluso pegar imágenes directamente dentro del texto para explicar tus estrategias paso a paso.
* Al terminar, haz clic en **Publicar** y tu guía quedará guardada y visible para que la comunidad empiece a valorarla.
