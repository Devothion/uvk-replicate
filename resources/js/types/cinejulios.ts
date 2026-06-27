/**
 * CINEJULIOS — Tipados TypeScript compartidos
 * ------------------------------------------------------------
 * Estos tipos describen las estructuras de datos que tu backend
 * Laravel inyectará como props de Inertia. Mantenlos sincronizados
 * con tus Eloquent Resources / API Resources para tener tipado
 * fuerte de punta a punta.
 */

/** Clasificación por edades (rating de censura). */
export type Clasificacion = 'APT' | '+14' | '+18' | 'PG' | 'PG-13';

/** Formato de proyección de la sala/función. */
export type FormatoProyeccion = '2D' | '3D' | 'IMAX' | '4DX' | 'VIP';

/** Métodos de pago soportados en el checkout. */
export type MetodoPago = 'tarjeta' | 'yape' | 'plin' | 'paypal' | 'efectivo';

/** Estado de una butaca dentro del mapa de sala. */
export type EstadoButaca = 'libre' | 'seleccionada' | 'ocupada';

/** Rol del usuario autenticado. */
export type RolUsuario = 'admin' | 'cliente';

/* ----------------------------- Usuario ----------------------------- */

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string | null;
    role: RolUsuario;
    email_verified_at?: string | null;
}

/* ----------------------------- Géneros ----------------------------- */

export interface Genero {
    id: number;
    nombre: string;
    slug: string;
}

/* ----------------------------- Películas ----------------------------- */

/** Versión "ligera" de la película usada en grillas y banners. */
export interface MovieCard {
    id: number;
    titulo: string;
    slug: string;
    poster: string;
    backdrop?: string;
    clasificacion: Clasificacion;
    rating: number; // 0 a 10
    duracion?: number; // minutos
    generos: string[];
    formatos?: FormatoProyeccion[];
    estreno?: boolean;
}

/** Reparto / actor. */
export interface CastMember {
    nombre: string;
    personaje?: string;
    foto?: string | null;
}

/** Versión completa de la película para la página de detalle. */
export interface Movie extends MovieCard {
    sinopsis: string;
    trailer_url: string;
    director: string;
    reparto: CastMember[];
    fecha_estreno?: string;
    idioma?: string;
    subtitulos?: boolean;
}

/* ----------------------------- Sedes ----------------------------- */

export interface Sede {
    id: number;
    nombre: string;
    distrito: string;
    direccion: string;
    ciudad: string;
    imagen?: string;
    salas: number;
    formatos: FormatoProyeccion[];
    telefono?: string;
    lat?: number;
    lng?: number;
}

/* ----------------------------- Funciones ----------------------------- */

/** Una hora concreta de proyección. */
export interface FuncionHora {
    showtime_id: number;
    hora: string; // '14:30'
    formato: FormatoProyeccion;
    precio: number;
    disponibilidad?: 'alta' | 'media' | 'baja';
}

/** Agrupación de funciones por sala dentro de una sede. */
export interface SalaFunciones {
    sala: string; // 'Sala 3'
    formato: FormatoProyeccion;
    horas: FuncionHora[];
}

/** Funciones de una sede en una fecha dada. */
export interface SedeFunciones {
    sede_id: number;
    sede_nombre: string;
    distrito: string;
    salas: SalaFunciones[];
}

/**
 * Mapa de funciones agrupadas por fecha.
 * Ejemplo: { '2026-06-27': SedeFunciones[], '2026-06-28': SedeFunciones[] }
 */
export type ShowtimesByDate = Record<string, SedeFunciones[]>;

/* ----------------------------- Funciones (detalle) ----------------------------- */

export interface ShowtimeDetalle {
    id: number;
    movie_title: string;
    movie_poster: string;
    sala: string;
    sede: string;
    distrito: string;
    fecha: string; // '2026-06-27'
    hora: string; // '20:15'
    formato: FormatoProyeccion;
    price_base: number;
    filas: number; // número de filas de la sala
    columnas: number; // butacas por fila
}

/* ----------------------------- Dulcería ----------------------------- */

export interface Concession {
    id: number;
    nombre: string;
    descripcion?: string;
    precio: number;
    imagen?: string;
    categoria?: 'combo' | 'cancha' | 'bebida' | 'snack' | 'dulce';
}

/* ----------------------------- Promociones ----------------------------- */

export interface Promotion {
    id: number;
    titulo: string;
    descripcion: string;
    imagen: string;
    descuento?: string; // '2x1', '30% OFF'
    vigencia?: string;
    codigo?: string;
    color?: string; // clase tailwind o hex para el degradado
}

/* ----------------------------- Reserva (payload de envío) ----------------------------- */

export interface PaymentDetails {
    metodo: MetodoPago;
    card_number?: string;
    card_name?: string;
    card_expiry?: string;
    card_cvv?: string;
    yape_phone?: string;
}

export interface BookingPayload {
    showtime_id: number;
    seats: string[];
    concessions: Record<number, number>; // { id_producto: cantidad }
    promo_code: string;
    payment_details: PaymentDetails;
}

/* ----------------------------- Inertia: props globales ----------------------------- */

/** Forma de $page.props compartida por HandleInertiaRequests. */
export interface SharedProps {
    auth: {
        user: User | null;
    };
    flash?: {
        success?: string;
        error?: string;
    };
    [key: string]: unknown;
}
