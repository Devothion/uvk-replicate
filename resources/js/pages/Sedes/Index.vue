<script setup lang="ts">
/**
 * Sedes/Index.vue — Listado de sedes de CINEJULIOS en Lima.
 * Prop inyectada por SedeController@index:
 *   - sedes : Array de sedes.
 */
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CineLayout from '@/layouts/CineLayout.vue';
import SedeCard from '@/components/cine/SedeCard.vue';
import type { Sede } from '@/types/cinejulios';

const props = defineProps<{
    sedes: Sede[];
}>();

const zonaActiva = ref('Todas');
const zonas = computed(() => ['Todas', ...new Set(props.sedes.map((s) => s.ciudad))]);
const filtradas = computed(() =>
    zonaActiva.value === 'Todas' ? props.sedes : props.sedes.filter((s) => s.ciudad === zonaActiva.value),
);
</script>

<template>
    <Head title="Nuestras sedes" />
    <CineLayout>
        <section class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <header class="text-center">
                <h1 class="text-4xl font-black text-foreground">Nuestras sedes</h1>
                <p class="mt-2 text-muted-foreground">Encuentra el CINEJULIOS más cercano a ti en Lima.</p>
            </header>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <button
                    v-for="z in zonas"
                    :key="z"
                    type="button"
                    @click="zonaActiva = z"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-all"
                    :class="zonaActiva === z ? 'bg-gradient-to-r from-red-600 to-amber-500 text-white' : 'border border-border text-muted-foreground hover:bg-accent hover:text-foreground'"
                >
                    {{ z }}
                </button>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <SedeCard v-for="s in filtradas" :key="s.id" :sede="s" />
            </div>
        </section>
    </CineLayout>
</template>
