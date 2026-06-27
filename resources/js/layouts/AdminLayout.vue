<script setup lang="ts">
/**
 * AdminLayout — Layout del panel de administración de CINEJULIOS.
 * --------------------------------------------------------------
 * Sidebar fijo con navegación + topbar. Pensado para las vistas/maquetas
 * del admin (Dashboard, Películas, Funciones, Sedes, Reservas).
 *
 * Solo lo verá un usuario con role === 'admin' (proteger con middleware
 * en el backend). Recibe un `title` opcional para la cabecera.
 */
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { SharedProps } from '@/types/cinejulios';

defineProps<{
    title?: string;
}>();

const page = usePage<SharedProps>();
const user = computed(() => page.props.auth?.user ?? null);
const sidebarOpen = ref(false);

const nav = [
    { label: 'Dashboard', href: '/admin/dashboard', icon: '📊' },
    { label: 'Películas', href: '/admin/peliculas', icon: '🎬' },
    { label: 'Funciones', href: '/admin/funciones', icon: '🕐' },
    { label: 'Sedes', href: '/admin/sedes', icon: '📍' },
    { label: 'Dulcería', href: '/admin/dulceria', icon: '🍿' },
    { label: 'Promociones', href: '/admin/promociones', icon: '🏷️' },
    { label: 'Reservas', href: '/admin/reservas', icon: '🎟️' },
];

const isActive = (href: string) => page.url.startsWith(href);
const logout = () => router.post('/logout');
</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-white/10 bg-neutral-900/80 backdrop-blur-xl transition-transform lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-red-600 to-amber-500">🎬</span>
                <span class="font-black text-white">CINE<span class="text-amber-400">JULIOS</span></span>
                <span class="ml-1 rounded bg-amber-400/20 px-1.5 py-0.5 text-[10px] font-bold text-amber-300">ADMIN</span>
            </div>
            <nav class="space-y-1 p-3">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                    :class="isActive(item.href) ? 'bg-gradient-to-r from-red-600/20 to-amber-500/20 text-white' : 'text-neutral-400 hover:bg-white/5 hover:text-white'"
                >
                    <span>{{ item.icon }}</span> {{ item.label }}
                </Link>
            </nav>
            <div class="absolute inset-x-0 bottom-0 border-t border-white/10 p-3">
                <Link href="/" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-neutral-400 hover:bg-white/5 hover:text-white">🌐 Ver sitio público</Link>
            </div>
        </aside>

        <!-- Backdrop móvil -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Contenido -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-white/10 bg-neutral-950/70 px-4 backdrop-blur-xl sm:px-6">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Menú">☰</button>
                    <h1 class="text-lg font-black text-white">{{ title ?? 'Panel de administración' }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden text-sm text-neutral-400 sm:inline">{{ user?.name }}</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-red-600 to-amber-500 text-xs font-bold text-white">
                        {{ user?.name?.charAt(0) ?? 'A' }}
                    </span>
                    <button type="button" @click="logout" class="rounded-lg border border-white/10 px-3 py-1.5 text-sm text-red-400 hover:bg-red-500/10">Salir</button>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
