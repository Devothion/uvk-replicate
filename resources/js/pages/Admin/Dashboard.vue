<script setup lang="ts">
/**
 * Admin/Dashboard.vue — Panel de control (maqueta) de CINEJULIOS.
 * --------------------------------------------------------------
 * Muestra KPIs, gráficos simulados (ventas y ocupación) y tablas de
 * actividad reciente. Los gráficos usan Chart.js.
 *
 * REQUISITO: instala chart.js en tu proyecto:
 *   npm install chart.js
 *
 * Props sugeridas (inyéctalas desde Admin\DashboardController@index):
 *   - stats          : { ventasHoy, entradasHoy, ocupacion, reservasActivas }
 *   - ventasSemana   : { labels: string[], data: number[] }
 *   - topPeliculas   : { titulo, entradas, ingresos }[]
 *
 * Si no envías props, se usan datos de demostración.
 */
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Chart,
    BarController, BarElement,
    LineController, LineElement, PointElement,
    DoughnutController, ArcElement,
    CategoryScale, LinearScale, Tooltip, Legend, Filler,
} from 'chart.js';

Chart.register(
    BarController, BarElement, LineController, LineElement, PointElement,
    DoughnutController, ArcElement, CategoryScale, LinearScale, Tooltip, Legend, Filler,
);

interface Stats {
    ventasHoy: number;
    entradasHoy: number;
    ocupacion: number;
    reservasActivas: number;
}

const props = withDefaults(
    defineProps<{
        stats?: Stats;
        ventasSemana?: { labels: string[]; data: number[] };
        topPeliculas?: { titulo: string; entradas: number; ingresos: number }[];
    }>(),
    {
        stats: () => ({ ventasHoy: 18450, entradasHoy: 1240, ocupacion: 72, reservasActivas: 86 }),
        ventasSemana: () => ({
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            data: [12400, 9800, 11200, 14300, 21500, 28900, 24100],
        }),
        topPeliculas: () => [
            { titulo: 'Dunas del Tiempo', entradas: 4210, ingresos: 89400 },
            { titulo: 'El Último Héroe', entradas: 3180, ingresos: 67200 },
            { titulo: 'Sombras de Lima', entradas: 2740, ingresos: 54100 },
            { titulo: 'Risas y Caos', entradas: 1980, ingresos: 38600 },
            { titulo: 'Nocturno', entradas: 1420, ingresos: 27800 },
        ],
    },
);

const kpis = [
    { label: 'Ventas hoy', valor: `S/ ${props.stats.ventasHoy.toLocaleString('es-PE')}`, icon: '💰', trend: '+12%' },
    { label: 'Entradas vendidas', valor: props.stats.entradasHoy.toLocaleString('es-PE'), icon: '🎟️', trend: '+8%' },
    { label: 'Ocupación promedio', valor: `${props.stats.ocupacion}%`, icon: '🪑', trend: '+3%' },
    { label: 'Reservas activas', valor: props.stats.reservasActivas.toString(), icon: '📋', trend: '+5%' },
];

const ventasCanvas = ref<HTMLCanvasElement | null>(null);
const formatoCanvas = ref<HTMLCanvasElement | null>(null);

onMounted(() => {
    // Gráfico de ventas semanales (línea con área)
    if (ventasCanvas.value) {
        new Chart(ventasCanvas.value, {
            type: 'line',
            data: {
                labels: props.ventasSemana.labels,
                datasets: [{
                    label: 'Ventas (S/)',
                    data: props.ventasSemana.data,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.15)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#a3a3a3' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: '#a3a3a3' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                },
            },
        });
    }

    // Distribución por formato (doughnut)
    if (formatoCanvas.value) {
        new Chart(formatoCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ['2D', '3D', 'IMAX', '4DX', 'VIP'],
                datasets: [{
                    data: [45, 22, 15, 10, 8],
                    backgroundColor: ['#ef4444', '#f59e0b', '#fbbf24', '#fb923c', '#dc2626'],
                    borderColor: '#0a0a0a',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: '#a3a3a3' } } },
            },
        });
    }
});
</script>

<template>
    <Head title="Dashboard · Admin" />
    <AdminLayout title="Dashboard">
        <!-- KPIs -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="k in kpis" :key="k.label" class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">{{ k.icon }}</span>
                    <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-xs font-bold text-emerald-400">{{ k.trend }}</span>
                </div>
                <p class="mt-3 text-2xl font-black text-white">{{ k.valor }}</p>
                <p class="text-sm text-neutral-400">{{ k.label }}</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5 lg:col-span-2">
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Ventas de la semana</h2>
                <div class="mt-4" style="height: 300px;">
                    <canvas ref="ventasCanvas"></canvas>
                </div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-white">Por formato</h2>
                <div class="mt-4" style="height: 300px;">
                    <canvas ref="formatoCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- Top películas -->
        <div class="mt-6 rounded-2xl border border-white/10 bg-neutral-900/60 p-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white">Películas más vendidas</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-neutral-500">
                            <th class="pb-2 font-medium">#</th>
                            <th class="pb-2 font-medium">Película</th>
                            <th class="pb-2 text-right font-medium">Entradas</th>
                            <th class="pb-2 text-right font-medium">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(p, i) in topPeliculas" :key="p.titulo" class="border-b border-white/5">
                            <td class="py-3 text-neutral-500">{{ i + 1 }}</td>
                            <td class="py-3 font-medium text-white">{{ p.titulo }}</td>
                            <td class="py-3 text-right text-neutral-300">{{ p.entradas.toLocaleString('es-PE') }}</td>
                            <td class="py-3 text-right font-bold text-amber-400">S/ {{ p.ingresos.toLocaleString('es-PE') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
