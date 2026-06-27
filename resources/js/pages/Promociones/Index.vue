<script setup lang="ts">
/**
 * Promociones/Index.vue — Listado de promociones vigentes.
 * Prop inyectada por PromotionController@index:
 *   - promotions : Array de promociones.
 */
import { Head } from '@inertiajs/vue3';
import CineLayout from '@/layouts/CineLayout.vue';
import PromoCard from '@/components/cine/PromoCard.vue';
import type { Promotion } from '@/types/cinejulios';

defineProps<{
    promotions: Promotion[];
}>();
</script>

<template>
    <Head title="Promociones" />
    <CineLayout>
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <header class="text-center">
                <h1 class="text-4xl font-black text-foreground">Promociones</h1>
                <p class="mt-2 text-muted-foreground">Descuentos, combos y beneficios exclusivos de CINEJULIOS.</p>
            </header>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <PromoCard v-for="p in promotions" :key="p.id" :promo="p" />
            </div>
        </section>
    </CineLayout>
</template>
