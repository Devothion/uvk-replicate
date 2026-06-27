<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin\AdminMovieController — CRUD de películas (vistas en Admin/Movies/*).
 *
 * Las vistas .vue son maquetas; aquí está el esqueleto del CRUD para que
 * lo completes con tu modelo Eloquent Movie.
 */
class AdminMovieController extends Controller
{
    public function index(): Response
    {
        // $movies = Movie::latest()->get(); // mapea a MovieCard
        return Inertia::render('Admin/Movies/Index', [
            'movies' => [], // si lo dejas vacío, el componente usa datos demo
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Movies/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:movies,slug'],
            'sinopsis' => ['required', 'string'],
            'director' => ['nullable', 'string', 'max:255'],
            'duracion' => ['required', 'integer', 'min:1'],
            'clasificacion' => ['required', 'string'],
            'rating' => ['nullable', 'numeric', 'between:0,10'],
            'generos' => ['nullable', 'string'],
            'formatos' => ['array'],
            'trailer_url' => ['nullable', 'url'],
            'poster' => ['nullable', 'string'],
            'backdrop' => ['nullable', 'string'],
            'estreno' => ['boolean'],
            'activa' => ['boolean'],
        ]);

        // Movie::create($data); // + sincronizar géneros/formatos

        return redirect()->route('admin.movies.index')
            ->with('success', 'Película creada correctamente.');
    }

    public function edit(int $movie): Response
    {
        // $movie = Movie::findOrFail($movie);
        return Inertia::render('Admin/Movies/Create', [
            // 'movie' => $movie, // reutiliza el form para editar si lo deseas
        ]);
    }

    public function update(Request $request, int $movie): RedirectResponse
    {
        // Validación + Movie::findOrFail($movie)->update(...);
        return redirect()->route('admin.movies.index')
            ->with('success', 'Película actualizada.');
    }

    public function destroy(int $movie): RedirectResponse
    {
        // Movie::findOrFail($movie)->delete();
        return back()->with('success', 'Película eliminada.');
    }
}
