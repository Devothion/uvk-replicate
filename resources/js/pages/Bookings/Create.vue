<script setup lang="ts">
/**
 * Bookings/Create.vue — Flujo de compra de entradas (stepper de 4 pasos).
 * --------------------------------------------------------------
 * Props inyectadas por Inertia desde el BookingController@create:
 *   - showtime      : Datos de la función seleccionada.
 *   - occupiedSeats : Array de butacas ya vendidas (ej. ['A3','B12']).
 *   - concessions   : Array de productos de dulcería disponibles.
 *
 * Pasos:
 *   1. Selección de butacas (SeatMap, límite 10).
 *   2. Dulcería (tarjetas con +/-).
 *   3. Pago (useForm de Inertia → POST /reserva).
 *   4. Éxito / confirmación (código de reserva + QR simulado).
 *
 * El POST envía: { showtime_id, seats, concessions, promo_code, payment_details }.
 */
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CineLayout from '@/layouts/CineLayout.vue';
import SeatMap from '@/components/cine/SeatMap.vue';
import type { Concession, MetodoPago, ShowtimeDetalle } from '@/types/cinejulios';

const props = defineProps<{
    showtime: ShowtimeDetalle;
    occupiedSeats: string[];
    concessions: Concession[];
}>();

/* ----------------------------- Estado del stepper ----------------------------- */
const pasos = ['Butacas', 'Dulcería', 'Pago', 'Confirmación'];
const pasoActual = ref(0); // 0-indexado
const reservaConfirmada = ref(false);
const codigoReserva = ref('');

/* ----------------------------- Selección de butacas ----------------------------- */
const seatsSeleccionadas = ref<string[]>([]);
const avisoLimite = ref(false);

const mostrarAvisoLimite = () => {
    avisoLimite.value = true;
    setTimeout(() => (avisoLimite.value = false), 2500);
};

/* ----------------------------- Dulcería ----------------------------- */
// { id_producto: cantidad }
const carritoDulceria = ref<Record<number, number>>({});

const agregar = (id: number) => {
    carritoDulceria.value[id] = (carritoDulceria.value[id] ?? 0) + 1;
};
const quitar = (id: number) => {
    if (!carritoDulceria.value[id]) return;
    carritoDulceria.value[id] -= 1;
    if (carritoDulceria.value[id] <= 0) delete carritoDulceria.value[id];
};

/* ----------------------------- Cálculo de totales ----------------------------- */
const subtotalEntradas = computed(() => seatsSeleccionadas.value.length * props.showtime.price_base);

const subtotalDulceria = computed(() =>
    Object.entries(carritoDulceria.value).reduce((acc, [id, cant]) => {
        const prod = props.concessions.find((c) => c.id === Number(id));
        return acc + (prod ? prod.precio * cant : 0);
    }, 0),
);

/** Descuento aplicado por el código promocional (validación local de demo). */
const descuento = computed(() => {
    const code = form.promo_code.trim().toUpperCase();
    if (code === 'CINE2X1') return subtotalEntradas.value / 2;
    if (code === 'JULIOS20') return (subtotalEntradas.value + subtotalDulceria.value) * 0.2;
    return 0;
});

const total = computed(() => Math.max(0, subtotalEntradas.value + subtotalDulceria.value - descuento.value));

/* ----------------------------- Formulario de Inertia ----------------------------- */
const form = useForm({
    showtime_id: props.showtime.id,
    seats: [] as string[],
    concessions: {} as Record<number, number>,
    promo_code: '',
    payment_details: {
        metodo: 'tarjeta' as MetodoPago,
        card_number: '',
        card_name: '',
        card_expiry: '',
        card_cvv: '',
        yape_phone: '',
    },
});

const metodosPago: { id: MetodoPago; label: string; icon: string }[] = [
    { id: 'tarjeta', label: 'Tarjeta', icon: '💳' },
    { id: 'yape', label: 'Yape', icon: '📱' },
    { id: 'plin', label: 'Plin', icon: '💜' },
    { id: 'paypal', label: 'PayPal', icon: '🅿️' },
    { id: 'efectivo', label: 'Efectivo en taquilla', icon: '💵' },
];

/* ----------------------------- Navegación del stepper ----------------------------- */
const puedeAvanzar = computed(() => {
    if (pasoActual.value === 0) return seatsSeleccionadas.value.length > 0;
    return true;
});

const siguiente = () => {
    if (pasoActual.value < pasos.length - 1 && puedeAvanzar.value) pasoActual.value++;
};
const anterior = () => {
    if (pasoActual.value > 0) pasoActual.value--;
};

/* ----------------------------- Envío de la reserva ----------------------------- */
const procesarPago = () => {
    // Sincroniza el estado reactivo con el form antes de enviar.
    form.seats = [...seatsSeleccionadas.value];
    form.concessions = { ...carritoDulceria.value };

    form.post('/reserva', {
        preserveScroll: true,
        onSuccess: (page) => {
            // El backend debería devolver el código en flash o como prop.
            // Como respaldo de demo generamos uno local.
            // @ts-expect-error acceso dinámico a flash
            codigoReserva.value = page.props?.flash?.codigo ?? generarCodigoDemo();
            reservaConfirmada.value = true;
            pasoActual.value = 3;
        },
    });
};

/** Genera un código de reserva de demostración (respaldo si el backend no envía uno). */
const generarCodigoDemo = () => {
    const rnd = Math.random().toString(36).substring(2, 8).toUpperCase();
    return `CJ-${rnd}`;
};
</script>

<template>
    <Head title="Compra de entradas" />

    <CineLayout>
        <div class="mx-auto max-w-5xl px-4 py-24 sm:px-6 lg:px-8">
            <!-- Resumen de la función -->
            <header class="mb-8 flex flex-col gap-4 rounded-2xl border border-border bg-card p-5 sm:flex-row sm:items-center">
                <img :src="showtime.movie_poster" :alt="showtime.movie_title" class="h-24 w-16 rounded-lg object-cover" />
                <div class="flex-1">
                    <h1 class="text-xl font-black text-foreground">{{ showtime.movie_title }}</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        📍 {{ showtime.sede }} ({{ showtime.distrito }}) · {{ showtime.sala }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        📅 {{ showtime.fecha }} · 🕐 {{ showtime.hora }} ·
                        <span class="font-bold text-amber-500">{{ showtime.formato }}</span>
                    </p>
                </div>
            </header>

            <!-- Indicador de pasos -->
            <nav class="mb-10">
                <ol class="flex items-center">
                    <li v-for="(paso, i) in pasos" :key="paso" class="flex flex-1 items-center last:flex-none">
                        <div class="flex flex-col items-center">
                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-full border-2 text-sm font-bold transition-all"
                                :class="i <= pasoActual
                                    ? 'border-amber-400 bg-amber-400 text-neutral-900'
                                    : 'border-border text-muted-foreground/60'"
                            >
                                {{ i < pasoActual ? '✓' : i + 1 }}
                            </span>
                            <span class="mt-1 hidden text-xs sm:block" :class="i <= pasoActual ? 'text-foreground font-semibold' : 'text-muted-foreground'">{{ paso }}</span>
                        </div>
                        <div v-if="i < pasos.length - 1" class="mx-2 h-0.5 flex-1 transition-all" :class="i < pasoActual ? 'bg-amber-400' : 'bg-border'"></div>
                    </li>
                </ol>
            </nav>

            <!-- ============================ PASO 1: BUTACAS ============================ -->
            <section v-show="pasoActual === 0" class="rounded-2xl border border-border bg-card/40 p-6">
                <h2 class="text-lg font-black text-foreground">Selecciona tus butacas</h2>
                <p class="mt-1 text-sm text-muted-foreground">Máximo 10 butacas por compra.</p>

                <Transition enter-active-class="transition" enter-from-class="opacity-0" enter-to-class="opacity-100">
                    <p v-if="avisoLimite" class="mt-3 rounded-lg border border-red-500/20 bg-red-500/5 px-4 py-2 text-sm text-red-600 dark:text-red-300">
                        ⚠️ Solo puedes seleccionar hasta 10 butacas.
                    </p>
                </Transition>

                <div class="mt-8">
                    <SeatMap
                        v-model="seatsSeleccionadas"
                        :rows="showtime.filas"
                        :cols="showtime.columnas"
                        :occupied="occupiedSeats"
                        :max="10"
                        @limit="mostrarAvisoLimite"
                    />
                </div>

                <div v-if="seatsSeleccionadas.length" class="mt-6 flex flex-wrap items-center gap-2 rounded-xl border border-border bg-accent/40 p-4">
                    <span class="text-sm text-muted-foreground">Seleccionadas:</span>
                    <span v-for="s in seatsSeleccionadas" :key="s" class="rounded-md bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-300">{{ s }}</span>
                    <span class="ml-auto text-sm font-bold text-foreground">S/ {{ subtotalEntradas.toFixed(2) }}</span>
                </div>
            </section>

            <!-- ============================ PASO 2: DULCERÍA ============================ -->
            <section v-show="pasoActual === 1" class="rounded-2xl border border-border bg-card/40 p-6">
                <h2 class="text-lg font-black text-foreground">Agrega dulcería</h2>
                <p class="mt-1 text-sm text-muted-foreground">¡Disfruta la película con algo rico! (Opcional)</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article v-for="prod in concessions" :key="prod.id" class="flex flex-col overflow-hidden rounded-xl border border-border bg-card">
                        <img :src="prod.imagen" :alt="prod.nombre" class="h-32 w-full object-cover" />
                        <div class="flex flex-1 flex-col p-3">
                            <h3 class="text-sm font-bold text-foreground">{{ prod.nombre }}</h3>
                            <p class="mt-0.5 line-clamp-2 text-xs text-muted-foreground">{{ prod.descripcion }}</p>
                            <div class="mt-auto flex items-center justify-between pt-3">
                                <span class="text-sm font-bold text-amber-500">S/ {{ prod.precio.toFixed(2) }}</span>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="quitar(prod.id)"
                                        :disabled="!carritoDulceria[prod.id]"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-border text-foreground transition-colors hover:bg-accent disabled:opacity-30"
                                    >−</button>
                                    <span class="w-5 text-center text-sm font-bold text-foreground">{{ carritoDulceria[prod.id] ?? 0 }}</span>
                                    <button
                                        type="button"
                                        @click="agregar(prod.id)"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-red-600 to-amber-500 text-white transition-transform hover:scale-110"
                                    >+</button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-if="subtotalDulceria > 0" class="mt-6 flex items-center justify-between rounded-xl border border-border bg-accent/40 p-4">
                    <span class="text-sm text-muted-foreground">Subtotal dulcería</span>
                    <span class="text-sm font-bold text-foreground">S/ {{ subtotalDulceria.toFixed(2) }}</span>
                </div>
            </section>

            <!-- ============================ PASO 3: PAGO ============================ -->
            <section v-show="pasoActual === 2" class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="rounded-2xl border border-border bg-card/40 p-6">
                    <h2 class="text-lg font-black text-foreground">Método de pago</h2>

                    <!-- Selector de método -->
                    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <button
                            v-for="m in metodosPago"
                            :key="m.id"
                            type="button"
                            @click="form.payment_details.metodo = m.id"
                            class="flex flex-col items-center gap-1 rounded-xl border p-3 text-sm transition-all"
                            :class="form.payment_details.metodo === m.id
                                ? 'border-amber-400 bg-amber-400/10 text-foreground dark:text-white font-bold'
                                : 'border-border text-muted-foreground hover:bg-accent'"
                        >
                            <span class="text-xl">{{ m.icon }}</span>
                            {{ m.label }}
                        </button>
                    </div>

                    <!-- Formulario según método -->
                    <div class="mt-6 space-y-4">
                        <!-- Tarjeta -->
                        <template v-if="form.payment_details.metodo === 'tarjeta'">
                            <div>
                                <label class="text-sm text-muted-foreground font-semibold">Número de tarjeta</label>
                                <input v-model="form.payment_details.card_number" type="text" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" class="mt-1 w-full rounded-lg border border-border bg-input px-4 py-2.5 text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                            </div>
                            <div>
                                <label class="text-sm text-muted-foreground font-semibold">Nombre del titular</label>
                                <input v-model="form.payment_details.card_name" type="text" placeholder="Como aparece en la tarjeta" class="mt-1 w-full rounded-lg border border-border bg-input px-4 py-2.5 text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-muted-foreground font-semibold">Vencimiento</label>
                                    <input v-model="form.payment_details.card_expiry" type="text" placeholder="MM/AA" maxlength="5" class="mt-1 w-full rounded-lg border border-border bg-input px-4 py-2.5 text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                                </div>
                                <div>
                                    <label class="text-sm text-muted-foreground font-semibold">CVV</label>
                                    <input v-model="form.payment_details.card_cvv" type="password" maxlength="4" placeholder="•••" class="mt-1 w-full rounded-lg border border-border bg-input px-4 py-2.5 text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                                </div>
                            </div>
                        </template>

                        <!-- Yape / Plin -->
                        <template v-else-if="form.payment_details.metodo === 'yape' || form.payment_details.metodo === 'plin'">
                            <div class="flex flex-col items-center rounded-xl border border-border bg-accent/40 p-6 text-center">
                                <div class="grid h-40 w-40 place-content-center rounded-xl bg-white p-2 border border-border shadow-md">
                                    <!-- QR simulado -->
                                    <div class="grid h-32 w-32 grid-cols-8 gap-0.5">
                                        <span v-for="n in 64" :key="n" :class="Math.random() > 0.5 ? 'bg-neutral-900' : 'bg-white'"></span>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-muted-foreground">Escanea el QR desde tu app de {{ form.payment_details.metodo === 'yape' ? 'Yape' : 'Plin' }}</p>
                                <input v-model="form.payment_details.yape_phone" type="tel" placeholder="N° de celular (opcional)" class="mt-3 w-48 rounded-lg border border-border bg-input px-4 py-2 text-center text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                            </div>
                        </template>

                        <!-- PayPal -->
                        <template v-else-if="form.payment_details.metodo === 'paypal'">
                            <p class="rounded-xl border border-border bg-accent/40 p-6 text-center text-sm text-muted-foreground">
                                🅿️ Serás redirigido a PayPal para completar el pago de forma segura.
                            </p>
                        </template>

                        <!-- Efectivo -->
                        <template v-else>
                            <p class="rounded-xl border border-border bg-accent/40 p-6 text-center text-sm text-muted-foreground">
                                💵 Paga en la taquilla del cine. Tu reserva se mantendrá hasta 30 minutos antes de la función.
                            </p>
                        </template>

                        <!-- Código promocional -->
                        <div>
                            <label class="text-sm text-muted-foreground font-semibold">Código promocional</label>
                            <input v-model="form.promo_code" type="text" placeholder="Ej: CINE2X1 o JULIOS20" class="mt-1 w-full rounded-lg border border-dashed border-amber-500 bg-amber-500/5 px-4 py-2.5 font-mono uppercase text-amber-700 dark:text-amber-200 placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                            <p v-if="descuento > 0" class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">✓ Descuento aplicado: −S/ {{ descuento.toFixed(2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resumen lateral -->
                <aside class="h-fit rounded-2xl border border-border bg-card p-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-foreground">Resumen</h3>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-muted-foreground">Entradas ({{ seatsSeleccionadas.length }})</dt><dd class="text-foreground font-semibold">S/ {{ subtotalEntradas.toFixed(2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted-foreground">Dulcería</dt><dd class="text-foreground font-semibold">S/ {{ subtotalDulceria.toFixed(2) }}</dd></div>
                        <div v-if="descuento > 0" class="flex justify-between text-emerald-600 dark:text-emerald-400 font-semibold"><dt>Descuento</dt><dd>−S/ {{ descuento.toFixed(2) }}</dd></div>
                        <div class="flex justify-between border-t border-border pt-3 text-base font-black"><dt class="text-foreground">Total</dt><dd class="text-amber-500">S/ {{ total.toFixed(2) }}</dd></div>
                    </dl>
                </aside>
            </section>

            <!-- ============================ PASO 4: CONFIRMACIÓN ============================ -->
            <section v-show="pasoActual === 3" class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20 text-3xl">✅</div>
                <h2 class="mt-4 text-2xl font-black text-foreground">¡Reserva confirmada!</h2>
                <p class="mt-1 text-sm text-muted-foreground">Te enviamos los detalles a tu correo. Presenta este código en el cine.</p>

                <div class="mx-auto mt-6 max-w-sm rounded-2xl border border-border bg-card p-6 shadow-lg">
                    <p class="text-xs uppercase tracking-wider text-muted-foreground/80">Código de reserva</p>
                    <p class="mt-1 text-3xl font-black tracking-widest text-amber-500">{{ codigoReserva || 'CJ-XXXXXX' }}</p>

                    <!-- Código de barras simulado -->
                    <div class="mt-5 flex h-16 items-end justify-center gap-px">
                        <span v-for="n in 48" :key="n" class="w-1 bg-foreground" :style="{ height: (Math.random() * 60 + 30) + '%' }"></span>
                    </div>

                    <dl class="mt-5 space-y-1 text-left text-sm">
                        <div class="flex justify-between"><dt class="text-muted-foreground/80">Película</dt><dd class="text-foreground font-semibold">{{ showtime.movie_title }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted-foreground/80">Función</dt><dd class="text-foreground font-semibold">{{ showtime.fecha }} · {{ showtime.hora }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted-foreground/80">Sala</dt><dd class="text-foreground font-semibold">{{ showtime.sala }} ({{ showtime.formato }})</dd></div>
                        <div class="flex justify-between"><dt class="text-muted-foreground/80">Butacas</dt><dd class="text-foreground font-semibold">{{ seatsSeleccionadas.join(', ') }}</dd></div>
                        <div class="flex justify-between border-t border-border pt-1 font-bold"><dt class="text-muted-foreground">Total pagado</dt><dd class="text-amber-500 font-black">S/ {{ total.toFixed(2) }}</dd></div>
                    </dl>
                </div>

                <div class="mt-6 flex justify-center gap-3">
                    <Link href="/mis-reservas" class="rounded-xl border border-border bg-accent px-5 py-2.5 text-sm font-bold text-foreground hover:bg-accent/80">🎟️ Mis reservas</Link>
                    <Link href="/cartelera" class="rounded-xl bg-gradient-to-r from-red-600 to-amber-500 px-5 py-2.5 text-sm font-bold text-white transition-transform hover:scale-105">Volver a cartelera</Link>
                </div>
            </section>

            <!-- ============================ NAVEGACIÓN DEL STEPPER ============================ -->
            <div v-if="pasoActual < 3" class="mt-8 flex items-center justify-between">
                <button
                    type="button"
                    @click="anterior"
                    :disabled="pasoActual === 0"
                    class="rounded-xl border border-border px-6 py-2.5 text-sm font-semibold text-foreground transition-colors hover:bg-accent disabled:opacity-30"
                >
                    ← Atrás
                </button>

                <p class="hidden text-sm text-muted-foreground sm:block">
                    Total: <span class="font-bold text-amber-500">S/ {{ total.toFixed(2) }}</span>
                </p>

                <button
                    v-if="pasoActual < 2"
                    type="button"
                    @click="siguiente"
                    :disabled="!puedeAvanzar"
                    class="rounded-xl bg-gradient-to-r from-red-600 to-amber-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition-transform hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Continuar →
                </button>
                <button
                    v-else
                    type="button"
                    @click="procesarPago"
                    :disabled="form.processing"
                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition-transform hover:scale-105 disabled:opacity-50"
                >
                    {{ form.processing ? 'Procesando...' : `Pagar S/ ${total.toFixed(2)}` }}
                </button>
            </div>
        </div>
    </CineLayout>
</template>
