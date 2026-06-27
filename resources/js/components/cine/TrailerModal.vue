<script setup lang="ts">
/**
 * Modal reproductor de tráiler.
 * Acepta una URL de YouTube/Vimeo o MP4. Si detecta YouTube usa iframe
 * embebido; en caso contrario usa <video>.
 *
 * Uso:
 *   <TrailerModal v-model:open="trailerOpen" :url="movie.trailer_url" :title="movie.titulo" />
 */
import { computed } from 'vue';

const props = defineProps<{
    open: boolean;
    url: string;
    title?: string;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const close = () => emit('update:open', false);

/** Convierte un enlace de YouTube en su versión embed. Devuelve null si no es YouTube. */
const youtubeEmbed = computed(() => {
    const match = props.url?.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]+)/);
    return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1&rel=0` : null;
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
                @click.self="close"
            >
                <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl border border-white/10 bg-neutral-950 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                        <h3 class="text-sm font-bold text-white">🎬 Tráiler · {{ title }}</h3>
                        <button
                            type="button"
                            @click="close"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 transition-colors hover:bg-white/10 hover:text-white"
                            aria-label="Cerrar"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="aspect-video w-full bg-black">
                        <iframe
                            v-if="youtubeEmbed"
                            :src="youtubeEmbed"
                            class="h-full w-full"
                            title="Reproductor de tráiler"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                        <video
                            v-else
                            :src="url"
                            controls
                            autoplay
                            playsinline
                            webkit-playsinline
                            class="h-full w-full"
                        ></video>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
