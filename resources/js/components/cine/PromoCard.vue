<script setup lang="ts">
/**
 * Tarjeta de promoción moderna con degradado y badge de descuento.
 */
import type { Promotion } from '@/types/cinejulios';

defineProps<{
    promo: Promotion;
}>();
</script>

<template>
    <article
        class="group relative overflow-hidden rounded-2xl border border-border bg-card transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-red-600/10"
    >
        <div class="relative aspect-[16/9] overflow-hidden">
            <img
                :src="promo.imagen"
                :alt="promo.titulo"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-neutral-950/40 to-transparent"></div>
            <span
                v-if="promo.descuento"
                class="absolute left-3 top-3 rounded-lg bg-gradient-to-r from-red-600 to-amber-500 px-3 py-1 text-sm font-black text-white shadow-lg"
            >
                {{ promo.descuento }}
            </span>
        </div>
        <div class="p-4">
            <h3 class="text-base font-bold text-foreground">{{ promo.titulo }}</h3>
            <p class="mt-1 text-sm text-muted-foreground">{{ promo.descripcion }}</p>
            <div class="mt-3 flex items-center justify-between">
                <span v-if="promo.vigencia" class="text-xs text-muted-foreground/60">Hasta {{ promo.vigencia }}</span>
                <span
                    v-if="promo.codigo"
                    class="rounded-md border border-dashed border-amber-400/50 bg-amber-400/10 px-2 py-1 text-xs font-mono font-bold text-amber-300"
                >
                    {{ promo.codigo }}
                </span>
            </div>
        </div>
    </article>
</template>
