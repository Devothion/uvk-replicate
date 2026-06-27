
---

## 🚀 Arquitectura y Componentes Implementados

### 1. Controladores del Backend (`app/Http/Controllers/`)
- **`WelcomeController.php`**: Gestiona la landing page pública, consultando las películas destacadas (sliders de cartelera) y las promociones vigentes.
- **`MovieController.php`**: Provee el catálogo completo filtrado por géneros y ordenado por popularidad, además de renderizar la ficha técnica de la película junto a la agenda de funciones y horarios agrupados por sede.
- **`SedeController.php`**: Controla el catálogo de establecimientos/locales con filtros geográficos.
- **`PromotionController.php`**: Expone las ofertas activas, combos y cupones de descuento.
- **`BookingController.php`**: Administra el stepper de reserva (POST de compra) y renderiza la sección "Mis reservas" del usuario autenticado.
- **`NewsletterController.php`**: Registra suscripciones rápidas a boletines y correos promocionales.
- **`Admin/DashboardController.php`**: Calcula indicadores clave de rendimiento (KPIs) en tiempo real (ventas totales, tickets vendidos, consumo de dulcería, ocupación de salas).
- **`Admin/MovieController.php`**: Administra el CRUD (Creación, lectura, actualización y borrado) de películas en cartelera.

### 2. Base de Datos y Modelos
El esquema relacional cuenta con las siguientes entidades principales:
* **`Movie`**: Películas con atributos de título, sinopsis, póster, backdrop, tráiler, director, reparto, géneros y clasificación.
* **`Sede`**: Sedes físicas del cine en Lima Metropolitana con campos de dirección, salas, coordenadas y formatos soportados (2D, 3D, Prime).
* **`Showtime`**: Funciones asociadas a una película, sede, sala y formato, con precios y horarios.
* **`Booking`**: Reservas confirmadas con código único, butacas adquiridas, estado de la transacción (confirmada, usada, cancelada) y totales monetarios.
* **`Concession`**: Productos de confitería/dulcería con precios y descripción.
* **`BookingConcession`**: Relación asociativa de productos comprados en cada reserva.

### 3. Vistas Públicas de Cliente (`resources/js/pages/`)
* **`Welcome.vue`**: Landing page interactiva con slider de cartelera y tráiler emergente.
* **`Cartelera/Index.vue`**: Catálogo general interactivo con buscador de películas en vivo, filtros por géneros y selectores de ordenación.
* **`Sedes/Index.vue`**: Buscador de locales segmentados por distritos con detalles informativos.
* **`Promociones/Index.vue`**: Vista de ofertas y combos activos para compra presencial o canje web.
* **`Movies/Show.vue`**: Ficha técnica y cartelera detallada de la película seleccionada con selectores deslizables de fecha y horario.
* **`Bookings/Index.vue`**: Historial del cliente con estado de reservas, butacas y códigos únicos.
* **`Bookings/Create.vue`**: Flujo de reserva interactiva de **4 pasos (Stepper)**:
  1. **Butacas**: Selección de asientos con límite parametrizado en un mapa de asientos reactivo.
  2. **Dulcería**: Adición y sustracción interactiva de confitería y combos.
  3. **Pago**: Simulación de métodos de pago (Tarjetas, QR de Yape/Plin, PayPal, Efectivo en taquilla) y entrada de cupones con descuento automático (Ej: `CINE2X1`, `JULIOS20`).
  4. **Confirmación**: Ticket digital completo con desglose de precios, código de reserva y código de barras dinámico escaneable.

### 4. Vistas Administrativas (`resources/js/pages/Admin/`)
* **`Dashboard.vue`**: Métricas de negocio interactivas, gráficos de ventas de entradas vs dulcería y estado de ocupación de salas.
* **`Movies/Index.vue` & `Create.vue`**: Gestión integrada del inventario de películas del cine.

### 5. Componentes y Helpers (`resources/js/components/cine/`)
* **`NavBar.vue`**: Barra de navegación adaptativa con selector de tema unificado.
* **`AppFooter.vue`**: Pie de página estético con enlaces del sitio e input de suscripción al boletín.
* **`SeatMap.vue`**: Mapa interactivo de butacas que simula la curvatura de la pantalla y previene la selección de asientos reservados u ocupados.
* **`TrailerModal.vue`**: Reproductor de video flotante y accesible para visualizar tráileres de YouTube.
* **`MovieCard.vue` & `PromoCard.vue`**: Componentes reutilizables adaptables al modo claro y oscuro.

---

## 🛠️ Tecnologías y Dependencias
* **Backend**: Laravel v13, PHP 8.4
* **Frontend**: Inertia.js v3 (Inertia-Vue), Vue 3, TypeScript
* **Estilos**: Tailwind CSS v4 con variables semánticas adaptativas
* **Gráficos**: Chart.js (`chart.js` + `vue-chart-3`)
* **Bundler**: Vite
* **Rutas Tipadas**: Laravel Wayfinder (Generación automática de rutas estáticas tipadas en frontend)

---

## 💻 Instalación y Uso Local

1. Instala las dependencias del backend:
   ```bash
   composer install
   ```
2. Instala las dependencias del frontend:
   ```bash
   npm install
   ```
3. Configura tus variables de entorno en el archivo `.env` y corre las migraciones con sus seeders:
   ```bash
   php artisan migrate --seed
   ```
4. Levanta el servidor de desarrollo de Vite:
   ```bash
   npm run dev
   ```
5. Inicia el servidor de Laravel:
   ```bash
   php artisan serve
   ```
   *(O si usas EnvKit, puedes acceder directamente a la URL local configurada, ej: https://cinejulios.test)*

---

## 🎟️ Códigos de Descuento de Prueba (Demo)
Durante el proceso de pago (Paso 3 del stepper), puedes probar los siguientes códigos promocionales:
- **`CINE2X1`**: Aplica un 50% de descuento en el costo total de las entradas seleccionadas.
- **`JULIOS20`**: Aplica un 20% de descuento en el subtotal general (entradas + dulcería).
