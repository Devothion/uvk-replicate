<script setup lang="ts">
/**
 * Bookings/Index.vue — "Mis reservas" del usuario autenticado.
 * Prop inyectada por BookingController@index:
 *   - bookings : Array de reservas del usuario.
 */
import { Head, Link } from '@inertiajs/vue3';
import CineLayout from '@/layouts/CineLayout.vue';

interface ReservaResumen {
    id: number;
    codigo: string;
    pelicula: string;
    poster: string;
    sede: string;
    sala: string;
    fecha: string;
    hora: string;
    formato: string;
    butacas: string[];
    total: number;
    estado: 'confirmada' | 'usada' | 'cancelada';
}

defineProps<{
    bookings: ReservaResumen[];
}>();

const colorEstado = (estado: string) => {
    if (estado === 'confirmada') return 'border-emerald-500/20 bg-emerald-500/5 text-emerald-600 dark:text-emerald-300';
    if (estado === 'usada') return 'border-border bg-accent text-muted-foreground';
    return 'border-red-500/20 bg-red-500/5 text-red-600 dark:text-red-300';
};
</script>

<template>
    <Head title="Mis reservas" />
    <CineLayout>
        <section class="mx-auto max-w-4xl px-4 py-24 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-black text-foreground font-sans">🎟️ Mis reservas</h1>

            <div v-if="bookings.length" class="mt-8 space-y-4">
                <article v-for="b in bookings" :key="b.id" class="flex flex-col gap-4 rounded-2xl border border-border bg-card p-4 sm:flex-row shadow-sm">
                    <img :src="b.poster" :alt="b.pelicula" class="h-28 w-20 rounded-lg object-cover" />
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-bold text-foreground">{{ b.pelicula }}</h2>
                            <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize" :class="colorEstado(b.estado)">{{ b.estado }}</span>
                        </div>
                        <p class="mt-1 text-sm text-muted-foreground font-sans">📍 {{ b.sede }} · {{ b.sala }} ({{ b.formato }})</p>
                        <p class="text-sm text-muted-foreground font-sans">📅 {{ b.fecha }} · 🕐 {{ b.hora }}</p>
                        <p class="mt-1 text-sm text-muted-foreground font-sans">Butacas: <span class="text-foreground font-semibold">{{ b.butacas.join(', ') }}</span></p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="font-mono text-sm font-bold text-amber-500">{{ b.codigo }}</span>
                            <span class="text-sm font-bold text-foreground">S/ {{ b.total.toFixed(2) }}</span>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="mt-12 rounded-2xl border border-dashed border-border py-16 text-center">
                <p class="text-muted-foreground font-sans">Aún no tienes reservas.</p>
                <Link href="/cartelera" class="mt-4 inline-block rounded-xl bg-gradient-to-r from-red-600 to-amber-500 px-5 py-2.5 text-sm font-bold text-white transition-transform hover:scale-105">Ver cartelera</Link>
            </div>
        </section>
    </CineLayout>
</template>
