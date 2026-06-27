<script setup lang="ts">
/** Tarjeta de sede con imagen, distrito, dirección y formatos disponibles. */
import { Link } from '@inertiajs/vue3';
import type { Sede } from '@/types/cinejulios';

defineProps<{
    sede: Sede;
}>();
</script>

<template>
    <article class="group overflow-hidden rounded-2xl border border-border bg-card transition-all hover:-translate-y-1 hover:border-amber-400/40">
        <div class="relative aspect-[16/10] overflow-hidden">
            <img :src="sede.imagen" :alt="sede.nombre" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 to-transparent"></div>
            <h3 class="absolute bottom-3 left-4 text-lg font-black text-white">{{ sede.nombre }}</h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-muted-foreground">📍 {{ sede.direccion }}, {{ sede.distrito }}</p>
            <p class="mt-1 text-xs text-muted-foreground/60">{{ sede.salas }} salas · {{ sede.telefono }}</p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                <span v-for="f in sede.formatos" :key="f" class="rounded-md border border-border bg-accent px-2 py-0.5 text-xs font-semibold text-amber-600 dark:text-amber-300">{{ f }}</span>
            </div>
            <Link href="/cartelera" class="mt-4 inline-block text-sm font-semibold text-amber-400 hover:text-amber-300">Ver cartelera de esta sede →</Link>
        </div>
    </article>
</template>
