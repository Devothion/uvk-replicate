<script setup lang="ts">
/**
 * Movies/Show.vue — Detalle de película y selección de horario.
 * --------------------------------------------------------------
 * Props inyectadas por Inertia desde el MovieController@show:
 *   - movie           : Objeto completo de la película.
 *   - showtimesByDate : Funciones agrupadas por fecha → sedes → salas → horas.
 *
 * Funcionalidades:
 *   - Cabecera con poster, backdrop y sinopsis detallada.
 *   - Selector de fechas deslizable (tabs).
 *   - Horarios organizados por sede y formato. Al hacer clic en una hora
 *     navega a /reserva/{showtime_id} (página de compra).
 */
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CineLayout from '@/layouts/CineLayout.vue';
import TrailerModal from '@/components/cine/TrailerModal.vue';
import type { Movie, ShowtimesByDate } from '@/types/cinejulios';

const props = defineProps<{
    movie: Movie;
    showtimesByDate: ShowtimesByDate;
}>();

const trailerOpen = ref(false);

/* ----------------------------- Fechas ----------------------------- */
const fechas = computed(() => Object.keys(props.showtimesByDate));
const fechaActiva = ref<string>(fechas.value[0] ?? '');

/** Sedes/funciones de la fecha activa. */
const funcionesDelDia = computed(() => props.showtimesByDate[fechaActiva.value] ?? []);

/** Formatea 'YYYY-MM-DD' a un objeto legible para la tab. */
const formatearFecha = (iso: string) => {
    const d = new Date(iso + 'T00:00:00');
    const dia = d.toLocaleDateString('es-PE', { weekday: 'short' });
    const num = d.getDate();
    const mes = d.toLocaleDateString('es-PE', { month: 'short' });
    return { dia: dia.replace('.', ''), num, mes: mes.replace('.', '') };
};

/** Navega al flujo de compra con el id de la función seleccionada. */
const irAComprar = (showtimeId: number) => {
    router.visit(`/reserva/${showtimeId}`);
};

/** Color del badge según disponibilidad. */
const colorDisponibilidad = (disp?: string) => {
    if (disp === 'baja') return 'border-red-500/40 text-red-600 dark:text-red-300';
    if (disp === 'media') return 'border-amber-500/40 text-amber-600 dark:text-amber-300';
    return 'border-emerald-500/40 text-emerald-600 dark:text-emerald-300';
};
</script>

<template>
    <Head :title="movie.titulo" />

    <CineLayout>
        <!-- ============================ CABECERA ============================ -->
        <section class="relative isolate">
            <div class="absolute inset-0 -z-10 h-[420px]">
                <img :src="movie.backdrop || movie.poster" :alt="movie.titulo" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-neutral-950/85 to-neutral-950/50"></div>
            </div>

            <div class="mx-auto max-w-7xl px-4 pt-28 sm:px-6 lg:px-8">
                <Link href="/cartelera" class="mb-6 inline-flex items-center gap-1 text-sm text-neutral-300 hover:text-amber-400">
                    ← Volver a cartelera
                </Link>

                <div class="grid gap-8 md:grid-cols-[260px_1fr]">
                    <!-- Poster -->
                    <div class="mx-auto w-48 md:mx-0 md:w-full">
                        <img :src="movie.poster" :alt="movie.titulo" class="w-full rounded-2xl border border-white/10 shadow-2xl" />
                    </div>

                    <!-- Info -->
                    <div>
                        <h1 class="text-3xl font-black text-white sm:text-5xl">{{ movie.titulo }}</h1>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-neutral-300">
                            <span class="flex items-center gap-1 font-bold text-amber-400">⭐ {{ movie.rating.toFixed(1) }}</span>
                            <span class="rounded border border-white/20 px-1.5 py-0.5 text-xs">{{ movie.clasificacion }}</span>
                            <span>🕐 {{ movie.duracion }} min</span>
                            <span v-if="movie.idioma">🌐 {{ movie.idioma }}</span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span v-for="g in movie.generos" :key="g" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-neutral-300">{{ g }}</span>
                            <span v-for="f in movie.formatos" :key="f" class="rounded-full bg-gradient-to-r from-red-600/80 to-amber-500/80 px-3 py-1 text-xs font-bold text-white">{{ f }}</span>
                        </div>

                        <p class="mt-5 max-w-2xl text-base leading-relaxed text-neutral-300">{{ movie.sinopsis }}</p>

                        <dl class="mt-5 grid max-w-lg grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-neutral-500">Director</dt>
                                <dd class="font-semibold text-white">{{ movie.director }}</dd>
                            </div>
                            <div>
                                <dt class="text-neutral-500">Reparto</dt>
                                <dd class="font-semibold text-white">{{ movie.reparto.map((r) => r.nombre).slice(0, 3).join(', ') }}</dd>
                            </div>
                        </dl>

                        <button
                            type="button"
                            @click="trailerOpen = true"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-white/10"
                        >
                            ▶️ Ver tráiler
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================ REPARTO ============================ -->
        <section v-if="movie.reparto?.length" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-foreground">Reparto principal</h2>
            <div class="mt-5 flex gap-4 overflow-x-auto pb-2">
                <figure v-for="(actor, i) in movie.reparto" :key="i" class="w-28 shrink-0 text-center">
                    <img
                        :src="actor.foto || 'https://placehold.co/120x120/1f1f1f/666?text=' + actor.nombre.charAt(0)"
                        :alt="actor.nombre"
                        class="mx-auto h-24 w-24 rounded-full border border-border object-cover"
                    />
                    <figcaption class="mt-2">
                        <p class="truncate text-sm font-semibold text-foreground">{{ actor.nombre }}</p>
                        <p v-if="actor.personaje" class="truncate text-xs text-muted-foreground/75">{{ actor.personaje }}</p>
                    </figcaption>
                </figure>
            </div>
        </section>

        <!-- ============================ HORARIOS ============================ -->
        <section id="horarios" class="border-t border-border bg-secondary/10">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-black text-foreground">Selecciona tu función</h2>

                <!-- Tabs de fechas -->
                <div class="mt-6 flex gap-2 overflow-x-auto pb-2">
                    <button
                        v-for="f in fechas"
                        :key="f"
                        type="button"
                        @click="fechaActiva = f"
                        class="flex min-w-[72px] flex-col items-center rounded-xl border px-4 py-2 transition-all"
                        :class="fechaActiva === f
                            ? 'border-amber-500 bg-amber-500/10 text-amber-600 dark:text-amber-400'
                            : 'border-border text-muted-foreground hover:bg-accent hover:text-foreground'"
                    >
                        <span class="text-xs uppercase">{{ formatearFecha(f).dia }}</span>
                        <span class="text-xl font-black">{{ formatearFecha(f).num }}</span>
                        <span class="text-xs uppercase">{{ formatearFecha(f).mes }}</span>
                    </button>
                </div>

                <!-- Funciones por sede -->
                <div class="mt-8 space-y-6">
                    <article
                        v-for="sede in funcionesDelDia"
                        :key="sede.sede_id"
                        class="rounded-2xl border border-border bg-card p-5"
                    >
                        <header class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-foreground">📍 {{ sede.sede_nombre }}</h3>
                                <p class="text-xs text-muted-foreground">{{ sede.distrito }}</p>
                            </div>
                        </header>

                        <div class="mt-4 space-y-4">
                            <div v-for="(sala, idx) in sede.salas" :key="idx" class="border-t border-border pt-4">
                                <div class="mb-3 flex items-center gap-2">
                                    <span class="rounded-md bg-gradient-to-r from-red-600 to-amber-500 px-2.5 py-1 text-xs font-bold text-white">{{ sala.formato }}</span>
                                    <span class="text-sm text-muted-foreground">{{ sala.sala }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="hora in sala.horas"
                                        :key="hora.showtime_id"
                                        type="button"
                                        @click="irAComprar(hora.showtime_id)"
                                        class="group flex flex-col items-center rounded-xl border bg-accent/40 dark:bg-white/5 px-4 py-2 transition-all hover:scale-105 hover:border-amber-400 hover:bg-amber-400/10"
                                        :class="colorDisponibilidad(hora.disponibilidad)"
                                    >
                                        <span class="text-base font-bold text-foreground">{{ hora.hora }}</span>
                                        <span class="text-[10px] text-muted-foreground">S/ {{ hora.precio.toFixed(2) }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>

                    <div v-if="!funcionesDelDia.length" class="rounded-2xl border border-dashed border-border py-12 text-center text-muted-foreground">
                        No hay funciones disponibles para esta fecha.
                    </div>
                </div>
            </div>
        </section>

        <TrailerModal v-model:open="trailerOpen" :url="movie.trailer_url" :title="movie.titulo" />
    </CineLayout>
</template>
