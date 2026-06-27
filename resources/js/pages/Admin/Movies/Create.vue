<script setup lang="ts">
/**
 * Admin/Movies/Create.vue — Formulario de creación de película (maqueta).
 * Usa useForm de Inertia y envía POST a /admin/peliculas.
 * Tú implementarás la validación y persistencia en el backend.
 */
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const form = useForm({
    titulo: '',
    slug: '',
    sinopsis: '',
    director: '',
    duracion: 120,
    clasificacion: 'PG-13',
    rating: 0,
    generos: '',
    formatos: [] as string[],
    trailer_url: '',
    poster: '',
    backdrop: '',
    estreno: false,
    activa: true,
});

const formatosDisponibles = ['2D', '3D', 'IMAX', '4DX', 'VIP'];
const clasificaciones = ['APT', 'PG', 'PG-13', '+14', '+18'];

const toggleFormato = (f: string) => {
    const i = form.formatos.indexOf(f);
    if (i >= 0) form.formatos.splice(i, 1);
    else form.formatos.push(f);
};

const submit = () => {
    form.post('/admin/peliculas');
};
</script>

<template>
    <Head title="Nueva película · Admin" />
    <AdminLayout title="Nueva película">
        <Link href="/admin/peliculas" class="mb-4 inline-block text-sm text-neutral-400 hover:text-amber-400">← Volver al listado</Link>

        <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
            <!-- Columna principal -->
            <div class="space-y-5 lg:col-span-2">
                <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Información general</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm text-neutral-300">Título</label>
                            <input v-model="form.titulo" type="text" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                            <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-400">{{ form.errors.titulo }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-neutral-300">Slug (URL)</label>
                            <input v-model="form.slug" type="text" placeholder="dunas-del-tiempo" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm text-neutral-300">Sinopsis</label>
                            <textarea v-model="form.sinopsis" rows="4" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="text-sm text-neutral-300">Director</label>
                            <input v-model="form.director" type="text" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div>
                            <label class="text-sm text-neutral-300">Géneros (separados por coma)</label>
                            <input v-model="form.generos" type="text" placeholder="Acción, Aventura" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Multimedia</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="text-sm text-neutral-300">URL del tráiler (YouTube)</label>
                            <input v-model="form.trailer_url" type="url" placeholder="https://youtube.com/watch?v=..." class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm text-neutral-300">URL del póster</label>
                                <input v-model="form.poster" type="url" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                            </div>
                            <div>
                                <label class="text-sm text-neutral-300">URL del backdrop</label>
                                <input v-model="form.backdrop" type="url" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna lateral -->
            <div class="space-y-5">
                <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Detalles</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="text-sm text-neutral-300">Clasificación</label>
                            <select v-model="form.clasificacion" class="mt-1 w-full rounded-lg border border-white/10 bg-neutral-900 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none">
                                <option v-for="c in clasificaciones" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm text-neutral-300">Duración (min)</label>
                                <input v-model.number="form.duracion" type="number" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                            </div>
                            <div>
                                <label class="text-sm text-neutral-300">Rating</label>
                                <input v-model.number="form.rating" type="number" step="0.1" max="10" class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-amber-400 focus:outline-none" />
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-neutral-300">Formatos</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button v-for="f in formatosDisponibles" :key="f" type="button" @click="toggleFormato(f)" class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition-all" :class="form.formatos.includes(f) ? 'border-amber-400 bg-amber-400/10 text-amber-300' : 'border-white/10 text-neutral-400'">{{ f }}</button>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-neutral-300">
                            <input v-model="form.estreno" type="checkbox" class="rounded border-white/20 bg-white/5 text-amber-400" /> Marcar como estreno
                        </label>
                        <label class="flex items-center gap-2 text-sm text-neutral-300">
                            <input v-model="form.activa" type="checkbox" class="rounded border-white/20 bg-white/5 text-amber-400" /> Activa en cartelera
                        </label>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full rounded-xl bg-gradient-to-r from-red-600 to-amber-500 py-3 text-sm font-bold text-white shadow-lg transition-transform hover:scale-[1.02] disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : '💾 Guardar película' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
