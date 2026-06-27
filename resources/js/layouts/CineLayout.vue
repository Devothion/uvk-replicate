<script setup lang="ts">
/**
 * CineLayout — Layout público principal de CINEJULIOS.
 * --------------------------------------------------------------
 * Envuelve las páginas públicas (Welcome, Movies/Show, Bookings/Create,
 * Sedes, Promociones) con la NavBar superior y el AppFooter.
 *
 * Se llama "CineLayout" para no colisionar con el AppLayout.vue
 * existente de tu starter kit. Úsalo así en una página:
 *
 *   [script setup lang="ts"]
 *   import CineLayout from '@/layouts/CineLayout.vue';
 *   [/script]
 *   [template]
 *     <CineLayout>
 *       ... contenido de la página ...
 *     </CineLayout>
 *   [/template]
 *
 * Muestra automáticamente los mensajes flash ($page.props.flash).
 */
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import NavBar from '@/components/cine/NavBar.vue';
import AppFooter from '@/components/cine/AppFooter.vue';
import type { SharedProps } from '@/types/cinejulios';

const page = usePage<SharedProps>();

const showFlash = ref(false);
const flash = computed(() => page.props.flash ?? {});

// Muestra el toast cuando llega un flash y lo oculta tras 4s.
watch(
    () => page.props.flash,
    (value) => {
        if (value?.success || value?.error) {
            showFlash.value = true;
            setTimeout(() => (showFlash.value = false), 4000);
        }
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground antialiased selection:bg-amber-400/30">
        <NavBar />

        <!-- Toast de mensajes flash -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="showFlash && (flash.success || flash.error)"
                class="fixed right-4 top-20 z-[60] max-w-sm rounded-xl border px-4 py-3 text-sm shadow-2xl backdrop-blur-xl"
                :class="flash.success
                    ? 'border-emerald-400/30 bg-emerald-500/15 text-emerald-200'
                    : 'border-red-400/30 bg-red-500/15 text-red-200'"
            >
                {{ flash.success || flash.error }}
            </div>
        </Transition>

        <main class="flex-1">
            <slot />
        </main>

        <AppFooter />
    </div>
</template>
