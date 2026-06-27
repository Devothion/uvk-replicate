<script setup lang="ts">
/**
 * Welcome.vue — Página de inicio / Landing de CINEJULIOS.
 * --------------------------------------------------------------
 * Props inyectadas por Inertia desde el WelcomeController de Laravel:
 *   - featuredMovie : Película destacada para el hero banner.
 *   - movies        : Array de películas activas en cartelera.
 *   - promotions    : Array de promociones vigentes.
 *
 * Funcionalidades:
 *   - Hero banner animado con "Comprar entradas" y "Ver tráiler" (modal).
 *   - Rejilla de cartelera con filtros de género y búsqueda reactiva.
 *   - Sección de promociones en tarjetas.
 */
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CineLayout from '@/layouts/CineLayout.vue';
import MovieCard from '@/components/cine/MovieCard.vue';
import PromoCard from '@/components/cine/PromoCard.vue';
import TrailerModal from '@/components/cine/TrailerModal.vue';
import type { Movie, MovieCard as MovieCardType, Promotion } from '@/types/cinejulios';

const props = defineProps<{
    featuredMovie: Movie;
    movies: MovieCardType[];
    promotions: Promotion[];
}>();

/* ----------------------------- Tráiler ----------------------------- */
const trailerOpen = ref(false);

/* ----------------------------- Filtros y búsqueda ----------------------------- */
const search = ref('');
const generoActivo = ref<string>('Todos');

/** Lista única de géneros disponibles a partir de las películas. */
const generos = computed(() => {
    const set = new Set<string>();
    props.movies.forEach((m) => m.generos.forEach((g) => set.add(g)));
    return ['Todos', ...Array.from(set).sort()];
});

/** Películas filtradas por género y por término de búsqueda. */
const peliculasFiltradas = computed(() => {
    return props.movies.filter((m) => {
        const coincideGenero = generoActivo.value === 'Todos' || m.generos.includes(generoActivo.value);
        const coincideBusqueda = m.titulo.toLowerCase().includes(search.value.trim().toLowerCase());
        return coincideGenero && coincideBusqueda;
    });
});
</script>

<template>
    <Head title="Cartelera y estrenos" />

    <CineLayout>
        <!-- ============================ HERO ============================ -->
        <section class="relative isolate overflow-hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 -z-10">
                <img
                    :src="featuredMovie.backdrop || featuredMovie.poster"
                    :alt="featuredMovie.titulo"
                    class="h-full w-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-neutral-950 via-neutral-950/80 to-neutral-950/30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-transparent to-neutral-950/40"></div>
            </div>

            <div class="mx-auto flex max-w-7xl flex-col justify-center px-4 py-24 sm:px-6 md:py-36 lg:px-8">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-300">
                        🔥 Película destacada
                    </span>
                    <h1 class="mt-4 text-4xl font-black leading-tight text-white drop-shadow-lg sm:text-6xl">
                        {{ featuredMovie.titulo }}
                    </h1>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-neutral-300">
                        <span class="flex items-center gap-1 font-semibold text-amber-400">⭐ {{ featuredMovie.rating.toFixed(1) }}</span>
                        <span class="rounded border border-white/20 px-1.5 py-0.5 text-xs">{{ featuredMovie.clasificacion }}</span>
                        <span v-if="featuredMovie.duracion">{{ featuredMovie.duracion }} min</span>
                        <span>{{ featuredMovie.generos.join(' · ') }}</span>
                    </div>
                    <p class="mt-4 line-clamp-3 max-w-xl text-base text-neutral-300">
                        {{ featuredMovie.sinopsis }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="`/pelicula/${featuredMovie.slug}`"
                            class="rounded-xl bg-gradient-to-r from-red-600 to-amber-500 px-6 py-3 text-sm font-bold text-white shadow-xl shadow-red-600/30 transition-transform hover:scale-105"
                        >
                            🎟️ Comprar entradas
                        </Link>
                        <button
                            type="button"
                            @click="trailerOpen = true"
                            class="rounded-xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-bold text-white backdrop-blur transition-colors hover:bg-white/10"
                        >
                            ▶️ Ver tráiler
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================ CARTELERA ============================ -->
        <section id="cartelera" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-black text-foreground">Cartelera</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Las películas que puedes ver hoy en CINEJULIOS.</p>
                </div>
                <!-- Buscador reactivo -->
                <div class="relative w-full sm:w-72">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">🔍</span>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Buscar película..."
                        class="w-full rounded-xl border border-border bg-input py-2.5 pl-10 pr-4 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                    />
                </div>
            </div>

            <!-- Filtros de género -->
            <div class="mt-6 flex flex-wrap gap-2">
                <button
                    v-for="g in generos"
                    :key="g"
                    type="button"
                    @click="generoActivo = g"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-all"
                    :class="generoActivo === g
                        ? 'bg-gradient-to-r from-red-600 to-amber-500 text-white shadow-lg'
                        : 'border border-border text-muted-foreground hover:bg-accent hover:text-foreground'"
                >
                    {{ g }}
                </button>
            </div>

            <!-- Rejilla -->
            <div
                v-if="peliculasFiltradas.length"
                class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
            >
                <MovieCard v-for="m in peliculasFiltradas" :key="m.id" :movie="m" />
            </div>
            <div v-else class="mt-12 rounded-2xl border border-dashed border-border py-16 text-center text-muted-foreground">
                😕 No encontramos películas con esos filtros.
            </div>
        </section>

        <!-- ============================ PROMOCIONES ============================ -->
        <section id="promociones" class="border-t border-border bg-secondary/10">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-foreground">Promociones</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Aprovecha nuestros descuentos y combos.</p>
                    </div>
                    <Link href="/promociones" class="text-sm font-semibold text-amber-400 hover:text-amber-300">Ver todas →</Link>
                </div>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <PromoCard v-for="p in promotions" :key="p.id" :promo="p" />
                </div>
            </div>
        </section>

        <!-- Modal de tráiler de la película destacada -->
        <TrailerModal v-model:open="trailerOpen" :url="featuredMovie.trailer_url" :title="featuredMovie.titulo" />
    </CineLayout>
</template>
