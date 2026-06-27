<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin\DashboardController — Panel de control (Admin/Dashboard.vue).
 *
 * Protégelo con middleware de rol admin. Las props son opcionales:
 * el componente trae datos de demo si no las envías.
 */
class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'ventasHoy' => 18450,
                'entradasHoy' => 1240,
                'ocupacion' => 72,
                'reservasActivas' => 86,
            ],
            'ventasSemana' => [
                'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                'data' => [12400, 9800, 11200, 14300, 21500, 28900, 24100],
            ],
            'topPeliculas' => [
                ['titulo' => 'Dunas del Tiempo', 'entradas' => 4210, 'ingresos' => 89400],
                ['titulo' => 'El Último Héroe', 'entradas' => 3180, 'ingresos' => 67200],
                ['titulo' => 'Sombras de Lima', 'entradas' => 2740, 'ingresos' => 54100],
            ],
        ]);
    }
}
