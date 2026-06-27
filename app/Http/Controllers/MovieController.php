<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * MovieController — Cartelera y detalle de película.
 *
 * - index() renderiza Cartelera/Index.vue con todas las películas.
 * - show()  renderiza Movies/Show.vue con la película y sus funciones
 *   agrupadas por fecha → sede → sala → hora.
 *
 * Reemplaza los datos de demo por consultas Eloquent reales.
 */
class MovieController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cartelera/Index', [
            'movies' => $this->demoMovies(),
        ]);
    }

    public function show(string $slug): Response
    {
        // En real: $movie = Movie::where('slug', $slug)->firstOrFail();
        return Inertia::render('Movies/Show', [
            'movie' => [
                'id' => 1,
                'titulo' => 'Dunas del Tiempo',
                'slug' => $slug,
                'poster' => 'https://placehold.co/400x600/0a0a0a/f59e0b?text=Dunas',
                'backdrop' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1600',
                'clasificacion' => '+14',
                'rating' => 8.7,
                'duracion' => 155,
                'generos' => ['Ciencia Ficción', 'Aventura'],
                'formatos' => ['2D', '3D', 'IMAX'],
                'sinopsis' => 'En un futuro donde el tiempo es la moneda más valiosa, un joven debe atravesar dunas infinitas para salvar a la humanidad de su propio destino. Una épica visualmente deslumbrante sobre el sacrificio y la esperanza.',
                'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'director' => 'Ana Salvatierra',
                'idioma' => 'Español / Subtitulada',
                'reparto' => [
                    ['nombre' => 'Carlos Reyes', 'personaje' => 'Kael', 'foto' => null],
                    ['nombre' => 'Lucía Vargas', 'personaje' => 'Mira', 'foto' => null],
                    ['nombre' => 'Diego Paredes', 'personaje' => 'El Guardián', 'foto' => null],
                ],
            ],
            'showtimesByDate' => $this->demoShowtimes(),
        ]);
    }

    private function demoMovies(): array
    {
        return [
            ['id' => 1, 'titulo' => 'Dunas del Tiempo', 'slug' => 'dunas-del-tiempo', 'poster' => 'https://placehold.co/400x600/0a0a0a/f59e0b?text=Dunas', 'clasificacion' => '+14', 'rating' => 8.7, 'generos' => ['Ciencia Ficción', 'Aventura'], 'estreno' => true],
            ['id' => 2, 'titulo' => 'El Último Héroe', 'slug' => 'el-ultimo-heroe', 'poster' => 'https://placehold.co/400x600/0a0a0a/ef4444?text=Heroe', 'clasificacion' => 'PG-13', 'rating' => 8.1, 'generos' => ['Acción']],
            ['id' => 3, 'titulo' => 'Sombras de Lima', 'slug' => 'sombras-de-lima', 'poster' => 'https://placehold.co/400x600/0a0a0a/fbbf24?text=Sombras', 'clasificacion' => '+18', 'rating' => 7.9, 'generos' => ['Suspenso', 'Drama']],
            ['id' => 4, 'titulo' => 'Risas y Caos', 'slug' => 'risas-y-caos', 'poster' => 'https://placehold.co/400x600/0a0a0a/fb923c?text=Risas', 'clasificacion' => 'APT', 'rating' => 7.2, 'generos' => ['Comedia']],
        ];
    }

    /** Estructura agrupada por fecha (coincide con ShowtimesByDate de TS). */
    private function demoShowtimes(): array
    {
        $sede = fn ($id, $nombre, $distrito, $salas) => [
            'sede_id' => $id, 'sede_nombre' => $nombre, 'distrito' => $distrito, 'salas' => $salas,
        ];
        $sala = fn ($sala, $formato, $horas) => compact('sala', 'formato', 'horas');
        $hora = fn ($id, $h, $f, $p, $disp = 'alta') => [
            'showtime_id' => $id, 'hora' => $h, 'formato' => $f, 'precio' => $p, 'disponibilidad' => $disp,
        ];

        return [
            '2026-06-27' => [
                $sede(1, 'CINEJULIOS Lima Centro', 'Cercado de Lima', [
                    $sala('Sala 1', '2D', [$hora(101, '14:30', '2D', 18.00), $hora(102, '17:00', '2D', 18.00, 'media')]),
                    $sala('Sala IMAX', 'IMAX', [$hora(103, '20:15', 'IMAX', 32.00, 'baja')]),
                ]),
                $sede(2, 'CINEJULIOS Plaza Norte', 'Independencia', [
                    $sala('Sala 3', '3D', [$hora(104, '16:00', '3D', 24.00), $hora(105, '19:30', '3D', 24.00)]),
                ]),
            ],
            '2026-06-28' => [
                $sede(1, 'CINEJULIOS Lima Centro', 'Cercado de Lima', [
                    $sala('Sala 2', '2D', [$hora(106, '15:00', '2D', 18.00), $hora(107, '18:30', '2D', 18.00)]),
                ]),
            ],
        ];
    }
}
