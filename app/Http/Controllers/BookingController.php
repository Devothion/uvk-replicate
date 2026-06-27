<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * BookingController — Flujo de compra de entradas.
 *
 * - create($showtime) : renderiza Bookings/Create.vue (stepper).
 * - store(Request)    : recibe el POST del stepper y crea la reserva.
 * - index()           : "Mis reservas" del usuario autenticado.
 *
 * El payload que envía el frontend (form.post('/reserva')) es:
 * {
 *   showtime_id: number,
 *   seats: string[],                       // ['A3','A4']
 *   concessions: { [id:number]: number },  // { 5: 2, 8: 1 }
 *   promo_code: string,
 *   payment_details: { metodo, card_number?, card_name?, ... }
 * }
 */
class BookingController extends Controller
{
    public function create(int $showtime): Response
    {
        // En real: $st = Showtime::with(['movie','sala.sede'])->findOrFail($showtime);
        return Inertia::render('Bookings/Create', [
            'showtime' => [
                'id' => $showtime,
                'movie_title' => 'Dunas del Tiempo',
                'movie_poster' => 'https://placehold.co/400x600/0a0a0a/f59e0b?text=Dunas',
                'sala' => 'Sala IMAX',
                'sede' => 'CINEJULIOS Lima Centro',
                'distrito' => 'Cercado de Lima',
                'fecha' => '2026-06-27',
                'hora' => '20:15',
                'formato' => 'IMAX',
                'price_base' => 32.00,
                'filas' => 8,
                'columnas' => 12,
            ],
            // Butacas ya vendidas (en real: desde la tabla de reservas).
            'occupiedSeats' => ['A3', 'A4', 'B7', 'C5', 'C6', 'D1', 'F10', 'F11', 'G8'],
            'concessions' => $this->demoConcessions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // 1) Validación del payload enviado por el frontend.
        $data = $request->validate([
            'showtime_id' => ['required', 'integer'],
            'seats' => ['required', 'array', 'min:1', 'max:10'],
            'seats.*' => ['string'],
            'concessions' => ['array'],
            'promo_code' => ['nullable', 'string', 'max:30'],
            'payment_details' => ['required', 'array'],
            'payment_details.metodo' => ['required', 'in:tarjeta,yape,plin,paypal,efectivo'],
        ]);

        // 2) Aquí: verificar que las butacas sigan libres, calcular el total,
        //    aplicar el código promocional, crear la reserva y los detalles,
        //    procesar el pago, etc.
        //
        //    $booking = Booking::create([...]);
        //    $booking->seats()->createMany(...);

        // 3) Generar un código único de reserva.
        $codigo = 'CJ-' . strtoupper(Str::random(6));

        // 4) Redirigir de vuelta enviando el código como flash.
        //    El frontend lo lee en page.props.flash.codigo (paso 4 del stepper).
        return back()->with([
            'success' => 'Reserva creada con éxito.',
            'codigo' => $codigo,
        ]);
    }

    public function index(): Response
    {
        // En real: $bookings = auth()->user()->bookings()->with(...)->latest()->get();
        return Inertia::render('Bookings/Index', [
            'bookings' => [
                [
                    'id' => 1, 'codigo' => 'CJ-A1B2C3', 'pelicula' => 'Dunas del Tiempo',
                    'poster' => 'https://placehold.co/200x300/0a0a0a/f59e0b?text=Dunas',
                    'sede' => 'CINEJULIOS Lima Centro', 'sala' => 'Sala IMAX',
                    'fecha' => '2026-06-27', 'hora' => '20:15', 'formato' => 'IMAX',
                    'butacas' => ['F5', 'F6'], 'total' => 64.00, 'estado' => 'confirmada',
                ],
            ],
        ]);
    }

    private function demoConcessions(): array
    {
        return [
            ['id' => 1, 'nombre' => 'Combo Dúo', 'descripcion' => 'Canchita grande + 2 gaseosas', 'precio' => 28.90, 'imagen' => 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=600', 'categoria' => 'combo'],
            ['id' => 2, 'nombre' => 'Canchita Gigante', 'descripcion' => 'Maíz dulce o salado', 'precio' => 18.50, 'imagen' => 'https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?w=600', 'categoria' => 'cancha'],
            ['id' => 3, 'nombre' => 'Gaseosa 32oz', 'descripcion' => 'Tu sabor favorito', 'precio' => 9.90, 'imagen' => 'https://images.unsplash.com/photo-1581636625402-29b2a704ef13?w=600', 'categoria' => 'bebida'],
            ['id' => 4, 'nombre' => 'Nachos con Queso', 'descripcion' => 'Crujientes con dip', 'precio' => 15.00, 'imagen' => 'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?w=600', 'categoria' => 'snack'],
        ];
    }
}
