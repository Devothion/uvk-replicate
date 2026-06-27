<script setup lang="ts">
/**
 * NavBar de CINEJULIOS
 * --------------------------------------------------------------
 * - Logo + enlaces públicos (Cartelera, Sedes, Promociones).
 * - Botón de alternar tema (claro/oscuro).
 * - Sección de perfil: si hay usuario autenticado muestra un
 *   dropdown con avatar/nombre, enlace al panel admin (si role==='admin')
 *   y botón de cerrar sesión (POST /logout). Si no, botones de
 *   Iniciar sesión / Registrarse.
 *
 * El usuario autenticado se lee de la prop global de Inertia:
 *   $page.props.auth.user
 */
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useTheme } from '@/composables/useTheme';
import type { SharedProps } from '@/types/cinejulios';

const page = usePage<SharedProps>();
const user = computed(() => page.props.auth?.user ?? null);
const isAdmin = computed(() => user.value?.role === 'admin');

const { theme, toggleTheme } = useTheme();

// Estados de UI
const mobileOpen = ref(false);
const dropdownOpen = ref(false);

// Enlaces públicos de navegación (las URLs coinciden con web.php)
const navLinks = [
    { label: 'Cartelera', href: '/cartelera' },
    { label: 'Sedes', href: '/sedes' },
    { label: 'Promociones', href: '/promociones' },
];

/** Cierra sesión enviando un POST a la ruta de Fortify/Laravel. */
const logout = () => {
    router.post('/logout');
};

/** Inicial del nombre para el avatar de respaldo. */
const userInitial = computed(() => user.value?.name?.charAt(0).toUpperCase() ?? 'U');
</script>

<template>
    <header
        class="sticky top-0 z-50 border-b border-border bg-background/70 backdrop-blur-xl supports-[backdrop-filter]:bg-background/60"
    >
        <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <Link href="/" class="group flex items-center gap-2">
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-red-600 to-amber-500 text-lg font-black text-white shadow-lg shadow-red-600/30 transition-transform group-hover:scale-105"
                >
                    🎬
                </span>
                <span class="text-xl font-black tracking-tight text-foreground">
                    CINE<span class="text-amber-400">JULIOS</span>
                </span>
            </Link>

            <!-- Enlaces públicos (desktop) -->
            <ul class="hidden items-center gap-1 md:flex">
                <li v-for="link in navLinks" :key="link.href">
                    <Link
                        :href="link.href"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    >
                        {{ link.label }}
                    </Link>
                </li>
            </ul>

            <!-- Acciones derechas -->
            <div class="flex items-center gap-2">
                <!-- Toggle de tema -->
                <button
                    type="button"
                    @click="toggleTheme"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground transition-colors hover:bg-accent hover:text-amber-500"
                    :aria-label="theme === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro'"
                >
                    <span v-if="theme === 'dark'">☀️</span>
                    <span v-else>🌙</span>
                </button>

                <!-- Usuario autenticado -->
                <div v-if="user" class="relative">
                    <button
                        type="button"
                        @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center gap-2 rounded-full border border-border py-1 pl-1 pr-3 transition-colors hover:bg-accent"
                    >
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                            class="h-7 w-7 rounded-full object-cover"
                        />
                        <span
                            v-else
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-red-600 to-amber-500 text-xs font-bold text-white"
                        >
                            {{ userInitial }}
                        </span>
                        <span class="hidden text-sm font-medium text-foreground sm:inline">{{ user.name }}</span>
                        <svg class="h-4 w-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-100 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1"
                    >
                        <div
                            v-if="dropdownOpen"
                            v-click-outside="() => (dropdownOpen = false)"
                            class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-border bg-popover p-1 shadow-2xl backdrop-blur-xl"
                        >
                            <div class="border-b border-border px-3 py-2">
                                <p class="truncate text-sm font-semibold text-foreground">{{ user.name }}</p>
                                <p class="truncate text-xs text-muted-foreground">{{ user.email }}</p>
                            </div>

                            <Link
                                href="/mis-reservas"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            >
                                🎟️ Mis reservas
                            </Link>
                            <Link
                                href="/settings/profile"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            >
                                ⚙️ Mi perfil
                            </Link>
                            <Link
                                v-if="isAdmin"
                                href="/admin/dashboard"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-amber-400 transition-colors hover:bg-amber-400/10"
                            >
                                🛠️ Panel de administración
                            </Link>

                            <button
                                type="button"
                                @click="logout"
                                class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-red-500 transition-colors hover:bg-red-500/10"
                            >
                                🚪 Cerrar sesión
                            </button>
                        </div>
                    </Transition>
                </div>

                <!-- Invitado -->
                <div v-else class="hidden items-center gap-2 sm:flex">
                    <Link
                        href="/login"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        href="/register"
                        class="rounded-lg bg-gradient-to-r from-red-600 to-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-600/30 transition-transform hover:scale-105"
                    >
                        Registrarse
                    </Link>
                </div>

                <!-- Botón hamburguesa (móvil) -->
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted-foreground md:hidden"
                    aria-label="Abrir menú"
                >
                    <span v-if="!mobileOpen">☰</span>
                    <span v-else>✕</span>
                </button>
            </div>
        </nav>

        <!-- Menú móvil -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="mobileOpen" class="border-t border-border bg-background/95 px-4 py-3 md:hidden">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="block rounded-lg px-4 py-3 text-sm font-medium text-foreground hover:bg-accent"
                >
                    {{ link.label }}
                </Link>
                <div v-if="!user" class="mt-2 grid grid-cols-2 gap-2 border-t border-white/10 pt-3">
                    <Link href="/login" class="rounded-lg border border-white/10 py-2 text-center text-sm text-white">
                        Iniciar sesión
                    </Link>
                    <Link href="/register" class="rounded-lg bg-gradient-to-r from-red-600 to-amber-500 py-2 text-center text-sm font-semibold text-white">
                        Registrarse
                    </Link>
                </div>
            </div>
        </Transition>
    </header>
</template>
