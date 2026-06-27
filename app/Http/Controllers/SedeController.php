<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * SedeController — Listado de sedes (Sedes/Index.vue).
 * Datos de demo realistas para Lima (Centro, Norte, Sur, Este).
 */
class SedeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Sedes/Index', [
            'sedes' => [
                ['id' => 1, 'nombre' => 'CINEJULIOS Lima Centro', 'distrito' => 'Cercado de Lima', 'direccion' => 'Av. Nicolás de Piérola 850', 'ciudad' => 'Centro', 'imagen' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=800', 'salas' => 8, 'formatos' => ['2D', '3D', 'IMAX'], 'telefono' => '(01) 555-1001'],
                ['id' => 2, 'nombre' => 'CINEJULIOS Plaza Norte', 'distrito' => 'Independencia', 'direccion' => 'Av. Alfredo Mendiola 1400', 'ciudad' => 'Norte', 'imagen' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800', 'salas' => 10, 'formatos' => ['2D', '3D', '4DX', 'VIP'], 'telefono' => '(01) 555-1002'],
                ['id' => 3, 'nombre' => 'CINEJULIOS Mall del Sur', 'distrito' => 'San Juan de Miraflores', 'direccion' => 'Av. Los Lirios 301', 'ciudad' => 'Sur', 'imagen' => 'https://images.unsplash.com/photo-1595769816263-9b910be24d5f?w=800', 'salas' => 7, 'formatos' => ['2D', '3D', 'VIP'], 'telefono' => '(01) 555-1003'],
                ['id' => 4, 'nombre' => 'CINEJULIOS Santa Anita', 'distrito' => 'Santa Anita', 'direccion' => 'Av. Carretera Central 111', 'ciudad' => 'Este', 'imagen' => 'https://images.unsplash.com/photo-1542204165-65bf26472b9b?w=800', 'salas' => 6, 'formatos' => ['2D', '3D', 'IMAX', '4DX'], 'telefono' => '(01) 555-1004'],
            ],
        ]);
    }
}
