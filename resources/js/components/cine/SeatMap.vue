<script setup lang="ts">
/**
 * SeatMap — Mapa interactivo de butacas de la sala.
 * --------------------------------------------------------------
 * Renderiza una cuadrícula de asientos con la curva de la pantalla.
 * Cada butaca puede estar: libre, seleccionada u ocupada.
 *
 * Usa v-model para sincronizar la lista de butacas seleccionadas:
 *   <SeatMap
 *     v-model="form.seats"
 *     :rows="showtime.filas"
 *     :cols="showtime.columnas"
 *     :occupied="occupiedSeats"
 *     :max="10"
 *   />
 *
 * Las butacas se identifican como 'A1', 'A2', ..., 'B1', etc.
 */
import { computed } from 'vue';
import type { EstadoButaca } from '@/types/cinejulios';

const props = withDefaults(
    defineProps<{
        modelValue: string[];
        rows?: number;
        cols?: number;
        occupied?: string[];
        max?: number;
    }>(),
    {
        rows: 8,
        cols: 12,
        occupied: () => [],
        max: 10,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string[]];
    limit: []; // se emite cuando se intenta superar el máximo
}>();

const ALFABETO = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

/** Etiquetas de filas, ej: ['A','B','C', ...] */
const filas = computed(() => ALFABETO.slice(0, props.rows).split(''));

/** Devuelve el estado visual de una butaca concreta. */
const estado = (seatId: string): EstadoButaca => {
    if (props.occupied.includes(seatId)) return 'ocupada';
    if (props.modelValue.includes(seatId)) return 'seleccionada';
    return 'libre';
};

/** Alterna la selección de una butaca respetando el límite. */
const toggle = (seatId: string) => {
    if (props.occupied.includes(seatId)) return; // ocupada: no se puede

    const seleccion = [...props.modelValue];
    const idx = seleccion.indexOf(seatId);

    if (idx >= 0) {
        seleccion.splice(idx, 1);
    } else {
        if (seleccion.length >= props.max) {
            emit('limit');
            return;
        }
        seleccion.push(seatId);
    }
    emit('update:modelValue', seleccion);
};
</script>

<template>
    <div class="select-none">
        <!-- Pantalla curva -->
        <div class="mb-8 flex flex-col items-center">
            <div class="h-2 w-2/3 rounded-[50%] bg-gradient-to-b from-amber-300/80 to-transparent shadow-[0_0_40px_rgba(251,191,36,0.5)]"></div>
            <span class="mt-2 text-xs font-semibold uppercase tracking-[0.3em] text-muted-foreground/60">Pantalla</span>
        </div>

        <!-- Cuadrícula de asientos -->
        <div class="mx-auto w-full overflow-x-auto">
            <div class="mx-auto flex min-w-max flex-col items-center gap-1.5">
                <div v-for="fila in filas" :key="fila" class="flex items-center gap-1.5">
                    <span class="w-5 text-center text-xs font-bold text-muted-foreground/80">{{ fila }}</span>
                    <button
                        v-for="col in cols"
                        :key="`${fila}${col}`"
                        type="button"
                        @click="toggle(`${fila}${col}`)"
                        :disabled="estado(`${fila}${col}`) === 'ocupada'"
                        :aria-label="`Butaca ${fila}${col} (${estado(`${fila}${col}`)})`"
                        class="h-7 w-7 rounded-t-lg border text-[10px] font-semibold transition-all duration-150"
                        :class="{
                            'border-border bg-accent text-muted-foreground hover:border-amber-500 hover:bg-amber-500/20 hover:text-foreground dark:bg-neutral-800': estado(`${fila}${col}`) === 'libre',
                            'scale-110 border-amber-400 bg-amber-400 text-neutral-900 shadow-lg shadow-amber-400/40': estado(`${fila}${col}`) === 'seleccionada',
                            'cursor-not-allowed border-transparent bg-red-500/10 text-red-600 dark:bg-red-950/40 dark:text-red-400': estado(`${fila}${col}`) === 'ocupada',
                        }"
                    >
                        {{ col }}
                    </button>
                    <span class="w-5 text-center text-xs font-bold text-muted-foreground/80">{{ fila }}</span>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-5 text-xs text-muted-foreground">
            <span class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-t border border-border bg-accent dark:bg-neutral-800"></span> Disponible
            </span>
            <span class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-t border border-amber-400 bg-amber-400"></span> Seleccionada
            </span>
            <span class="flex items-center gap-2">
                <span class="h-4 w-4 rounded-t bg-red-500/20 dark:bg-red-900/60"></span> Ocupada
            </span>
        </div>
    </div>
</template>
