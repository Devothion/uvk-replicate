<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * WelcomeController — Renderiza la landing (Welcome.vue).
 *
 * Aquí se muestran datos de DEMOSTRACIÓN para que el frontend funcione
 * de inmediato. Reemplaza los arrays por consultas Eloquent reales:
 *   - $featured = Movie::featured()->first();
 *   - $movies   = Movie::active()->with('generos')->get();
 *   - $promos   = Promotion::vigentes()->get();
 *
 * IMPORTANTE: las claves de cada array deben coincidir con los tipos
 * definidos en resources/js/types/cinejulios.ts.
 */
class WelcomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'featuredMovie' => [
                'id' => 1,
                'titulo' => 'Dunas del Tiempo',
                'slug' => 'dunas-del-tiempo',
                'poster' => 'https://placehold.co/400x600/0a0a0a/f59e0b?text=Dunas',
                'backdrop' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1600',
                'clasificacion' => '+14',
                'rating' => 8.7,
                'duracion' => 155,
                'generos' => ['Ciencia Ficción', 'Aventura'],
                'sinopsis' => 'En un futuro donde el tiempo es la moneda más valiosa, un joven debe atravesar dunas infinitas para salvar a la humanidad de su propio destino.',
                'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'director' => 'Ana Salvatierra',
                'reparto' => [],
            ],
            'movies' => $this->demoMovies(),
            'promotions' => $this->demoPromotions(),
        ]);
    }

    /** Datos de demo de películas (compatibles con MovieCard de TS). */
    private function demoMovies(): array
    {
        return [
            ['id' => 1, 'titulo' => 'Dunas del Tiempo', 'slug' => 'dunas-del-tiempo', 'poster' => 'https://placehold.co/400x600/0a0a0a/f59e0b?text=Dunas', 'clasificacion' => '+14', 'rating' => 8.7, 'generos' => ['Ciencia Ficción', 'Aventura'], 'estreno' => true],
            ['id' => 2, 'titulo' => 'El Último Héroe', 'slug' => 'el-ultimo-heroe', 'poster' => 'https://placehold.co/400x600/0a0a0a/ef4444?text=Heroe', 'clasificacion' => 'PG-13', 'rating' => 8.1, 'generos' => ['Acción']],
            ['id' => 3, 'titulo' => 'Sombras de Lima', 'slug' => 'sombras-de-lima', 'poster' => 'https://placehold.co/400x600/0a0a0a/fbbf24?text=Sombras', 'clasificacion' => '+18', 'rating' => 7.9, 'generos' => ['Suspenso', 'Drama']],
            ['id' => 4, 'titulo' => 'Risas y Caos', 'slug' => 'risas-y-caos', 'poster' => 'https://placehold.co/400x600/0a0a0a/fb923c?text=Risas', 'clasificacion' => 'APT', 'rating' => 7.2, 'generos' => ['Comedia']],
            ['id' => 5, 'titulo' => 'Nocturno', 'slug' => 'nocturno', 'poster' => 'https://placehold.co/400x600/0a0a0a/dc2626?text=Nocturno', 'clasificacion' => '+18', 'rating' => 8.4, 'generos' => ['Terror']],
        ];
    }

    /** Datos de demo de promociones (compatibles con Promotion de TS). */
    private function demoPromotions(): array
    {
        return [
            ['id' => 1, 'titulo' => 'Martes 2x1', 'descripcion' => 'Lleva dos entradas al precio de una todos los martes.', 'imagen' => 'https://images.unsplash.com/photo-1585647347483-22b66260dfff?w=800', 'descuento' => '2x1', 'vigencia' => '31 Dic', 'codigo' => 'CINE2X1'],
            ['id' => 2, 'titulo' => 'Combo Familiar', 'descripcion' => 'Canchita gigante + 2 bebidas con 30% de descuento.', 'imagen' => 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=800', 'descuento' => '30% OFF', 'vigencia' => '15 Jul'],
            ['id' => 3, 'titulo' => 'Estudiantes', 'descripcion' => 'Presenta tu carnet y obtén 20% en cualquier función.', 'imagen' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800', 'descuento' => '20% OFF', 'codigo' => 'JULIOS20'],
        ];
    }
}
