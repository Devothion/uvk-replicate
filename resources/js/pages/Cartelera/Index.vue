<script setup lang="ts">
/**
 * Cartelera/Index.vue — Listado completo de películas en cartelera.
 * Prop inyectada por MovieController@index:
 *   - movies : Array de películas.
 */
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CineLayout from '@/layouts/CineLayout.vue';
import MovieCard from '@/components/cine/MovieCard.vue';
import type { MovieCard as MovieCardType } from '@/types/cinejulios';

const props = defineProps<{
    movies: MovieCardType[];
}>();

const search = ref('');
const generoActivo = ref('Todos');
const orden = ref<'rating' | 'titulo'>('rating');

const generos = computed(() => {
    const set = new Set<string>();
    props.movies.forEach((m) => m.generos.forEach((g) => set.add(g)));
    return ['Todos', ...Array.from(set).sort()];
});

const resultado = computed(() => {
    let arr = props.movies.filter((m) => {
        const g = generoActivo.value === 'Todos' || m.generos.includes(generoActivo.value);
        const s = m.titulo.toLowerCase().includes(search.value.trim().toLowerCase());
        return g && s;
    });
    arr = [...arr].sort((a, b) => (orden.value === 'rating' ? b.rating - a.rating : a.titulo.localeCompare(b.titulo)));
    return arr;
});
</script>

<template>
    <Head title="Cartelera completa" />
    <CineLayout>
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-4xl font-black text-foreground">Cartelera</h1>
                    <p class="mt-2 text-muted-foreground">{{ resultado.length }} películas disponibles.</p>
                </div>
                <div class="flex gap-2">
                    <div class="relative w-full sm:w-64">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">🔍</span>
                        <input v-model="search" type="search" placeholder="Buscar..." class="w-full rounded-xl border border-border bg-input py-2.5 pl-10 pr-4 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-amber-500 focus:outline-none" />
                    </div>
                    <select v-model="orden" class="rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-foreground focus:border-amber-500 focus:outline-none">
                        <option value="rating">Mejor valoradas</option>
                        <option value="titulo">A-Z</option>
                    </select>
                </div>
            </header>

            <div class="mt-6 flex flex-wrap gap-2">
                <button
                    v-for="g in generos"
                    :key="g"
                    type="button"
                    @click="generoActivo = g"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-all"
                    :class="generoActivo === g ? 'bg-gradient-to-r from-red-600 to-amber-500 text-white' : 'border border-border text-muted-foreground hover:bg-accent hover:text-foreground'"
                >
                    {{ g }}
                </button>
            </div>

            <div v-if="resultado.length" class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                <MovieCard v-for="m in resultado" :key="m.id" :movie="m" />
            </div>
            <p v-else class="mt-12 text-center text-muted-foreground/60">😕 No hay resultados.</p>
        </section>
    </CineLayout>
</template>
