<script setup lang="ts">
/**
 * Footer público de CINEJULIOS.
 * Incluye enlaces de navegación, redes sociales, copyright y un
 * formulario de suscripción al boletín que envía un POST a /newsletter
 * usando useForm de Inertia.
 */
import { Link, useForm } from '@inertiajs/vue3';

const year = new Date().getFullYear();

// Formulario de suscripción al boletín (Inertia useForm)
const form = useForm({
    email: '',
});

const subscribe = () => {
    form.post('/newsletter', {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    });
};

const footerLinks = {
    Cine: [
        { label: 'Cartelera', href: '/cartelera' },
        { label: 'Próximos estrenos', href: '/cartelera?filtro=estrenos' },
        { label: 'Sedes', href: '/sedes' },
        { label: 'Promociones', href: '/promociones' },
    ],
    Ayuda: [
        { label: 'Preguntas frecuentes', href: '/faq' },
        { label: 'Términos y condiciones', href: '/terminos' },
        { label: 'Política de privacidad', href: '/privacidad' },
        { label: 'Contacto', href: '/contacto' },
    ],
};

const social = [
    { label: 'Facebook', icon: '📘', href: 'https://facebook.com' },
    { label: 'Instagram', icon: '📷', href: 'https://instagram.com' },
    { label: 'TikTok', icon: '🎵', href: 'https://tiktok.com' },
    { label: 'YouTube', icon: '▶️', href: 'https://youtube.com' },
];
</script>

<template>
    <footer class="border-t border-border bg-background text-muted-foreground">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-4">
                <!-- Marca + boletín -->
                <div class="md:col-span-2">
                    <Link href="/" class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-red-600 to-amber-500 text-lg">🎬</span>
                        <span class="text-xl font-black text-foreground">CINE<span class="text-amber-400">JULIOS</span></span>
                    </Link>
                    <p class="mt-4 max-w-sm text-sm text-muted-foreground">
                        La mejor experiencia de cine en Lima. Estrenos, formatos premium (3D, IMAX, 4DX, VIP) y la dulcería más rica.
                    </p>

                    <!-- Boletín -->
                    <form @submit.prevent="subscribe" class="mt-6 max-w-sm">
                        <label for="newsletter-email" class="text-sm font-semibold text-foreground">Suscríbete a nuestro boletín</label>
                        <div class="mt-2 flex gap-2">
                            <input
                                id="newsletter-email"
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="tu@correo.com"
                                class="w-full rounded-lg border border-border bg-input px-4 py-2 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                            />
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-gradient-to-r from-red-600 to-amber-500 px-4 py-2 text-sm font-semibold text-white transition-transform hover:scale-105 disabled:opacity-50"
                            >
                                {{ form.processing ? '...' : 'Unirme' }}
                            </button>
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
                    </form>
                </div>

                <!-- Columnas de enlaces -->
                <div v-for="(links, group) in footerLinks" :key="group">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-foreground">{{ group }}</h3>
                    <ul class="mt-4 space-y-2">
                        <li v-for="l in links" :key="l.href">
                            <Link :href="l.href" class="text-sm text-muted-foreground transition-colors hover:text-amber-500">{{ l.label }}</Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Barra inferior -->
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-border pt-6 sm:flex-row">
                <p class="text-xs text-muted-foreground/60">© {{ year }} CINEJULIOS. Todos los derechos reservados.</p>
                <div class="flex gap-3">
                    <a
                        v-for="s in social"
                        :key="s.label"
                        :href="s.href"
                        target="_blank"
                        rel="noopener"
                        :aria-label="s.label"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-border transition-colors hover:bg-accent hover:text-amber-500"
                    >
                        {{ s.icon }}
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
