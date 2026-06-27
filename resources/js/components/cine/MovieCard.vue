<script setup lang="ts">
/**
 * Tarjeta de película para la rejilla de cartelera.
 * Muestra poster, badge de clasificación, rating, géneros y CTA.
 * Al hacer clic navega al detalle vía <Link> de Inertia.
 */
import { Link } from '@inertiajs/vue3';
import type { MovieCard as MovieCardType } from '@/types/cinejulios';

defineProps<{
    movie: MovieCardType;
}>();
</script>

<template>
    <article
        class="group relative overflow-hidden rounded-2xl border border-border bg-card transition-all duration-300 hover:-translate-y-1 hover:border-amber-400/40 hover:shadow-2xl hover:shadow-amber-500/10"
    >
        <Link :href="`/pelicula/${movie.slug}`" class="block">
            <!-- Poster -->
            <div class="relative aspect-[2/3] overflow-hidden">
                <img
                    :src="movie.poster"
                    :alt="`Póster de ${movie.titulo}`"
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-transparent to-transparent opacity-60"></div>

                <!-- Badge estreno -->
                <span
                    v-if="movie.estreno"
                    class="absolute left-2 top-2 rounded-md bg-red-600 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-lg"
                >
                    Estreno
                </span>

                <!-- Clasificación -->
                <span class="absolute right-2 top-2 rounded-md bg-black/70 px-2 py-1 text-[10px] font-bold text-white backdrop-blur">
                    {{ movie.clasificacion }}
                </span>

                <!-- Overlay CTA al hover -->
                <div class="absolute inset-x-0 bottom-0 flex translate-y-4 items-center justify-center p-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                    <span class="rounded-lg bg-gradient-to-r from-red-600 to-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-lg">
                        Ver detalle →
                    </span>
                </div>
            </div>

            <!-- Información -->
            <div class="p-3">
                <h3 class="truncate text-sm font-bold text-foreground">{{ movie.titulo }}</h3>
                <div class="mt-1 flex items-center gap-2">
                    <span class="flex items-center gap-1 text-xs font-semibold text-amber-400">
                        ⭐ {{ movie.rating.toFixed(1) }}
                    </span>
                    <span class="text-muted-foreground/60">·</span>
                    <span class="truncate text-xs text-muted-foreground">{{ movie.generos.slice(0, 2).join(', ') }}</span>
                </div>
            </div>
        </Link>
    </article>
</template>
