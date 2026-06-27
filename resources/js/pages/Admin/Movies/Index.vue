<script setup lang="ts">
/**
 * Admin/Movies/Index.vue — Listado de películas (maqueta admin).
 * Prop sugerida: movies (array). Tú implementarás el CRUD real.
 */
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { MovieCard } from '@/types/cinejulios';

const props = withDefaults(
    defineProps<{ movies?: MovieCard[] }>(),
    {
        movies: () => [
            { id: 1, titulo: 'Dunas del Tiempo', slug: 'dunas-del-tiempo', poster: 'https://placehold.co/80x120/1f1f1f/f59e0b?text=DT', clasificacion: '+14', rating: 8.7, generos: ['Ciencia Ficción', 'Aventura'], estreno: true },
            { id: 2, titulo: 'El Último Héroe', slug: 'el-ultimo-heroe', poster: 'https://placehold.co/80x120/1f1f1f/ef4444?text=UH', clasificacion: 'PG-13', rating: 8.1, generos: ['Acción'] },
            { id: 3, titulo: 'Sombras de Lima', slug: 'sombras-de-lima', poster: 'https://placehold.co/80x120/1f1f1f/fbbf24?text=SL', clasificacion: '+18', rating: 7.9, generos: ['Suspenso', 'Drama'] },
        ] as MovieCard[],
    },
);

const search = ref('');
const filtradas = computed(() => props.movies.filter((m) => m.titulo.toLowerCase().includes(search.value.toLowerCase())));
</script>

<template>
    <Head title="Películas · Admin" />
    <AdminLayout title="Películas">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <input v-model="search" type="search" placeholder="Buscar película..." class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white sm:w-72 focus:border-amber-400 focus:outline-none" />
            <Link href="/admin/peliculas/crear" class="rounded-xl bg-gradient-to-r from-red-600 to-amber-500 px-5 py-2.5 text-center text-sm font-bold text-white">+ Nueva película</Link>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-neutral-900/60">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-white/10 text-neutral-500">
                        <th class="p-4 font-medium">Película</th>
                        <th class="p-4 font-medium">Clasificación</th>
                        <th class="p-4 font-medium">Rating</th>
                        <th class="p-4 font-medium">Géneros</th>
                        <th class="p-4 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="m in filtradas" :key="m.id" class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img :src="m.poster" :alt="m.titulo" class="h-14 w-10 rounded object-cover" />
                                <div>
                                    <p class="font-semibold text-white">{{ m.titulo }}</p>
                                    <p class="text-xs text-neutral-500">{{ m.slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4"><span class="rounded border border-white/20 px-1.5 py-0.5 text-xs text-neutral-300">{{ m.clasificacion }}</span></td>
                        <td class="p-4 text-amber-400">⭐ {{ m.rating.toFixed(1) }}</td>
                        <td class="p-4 text-neutral-400">{{ m.generos.join(', ') }}</td>
                        <td class="p-4 text-right">
                            <Link :href="`/admin/peliculas/${m.id}/editar`" class="rounded-lg border border-white/10 px-3 py-1.5 text-xs text-white hover:bg-white/5">Editar</Link>
                            <button type="button" class="ml-2 rounded-lg border border-red-400/30 px-3 py-1.5 text-xs text-red-400 hover:bg-red-500/10">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
