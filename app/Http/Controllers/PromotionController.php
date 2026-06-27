<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * PromotionController — Listado de promociones (Promociones/Index.vue).
 */
class PromotionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Promociones/Index', [
            'promotions' => [
                ['id' => 1, 'titulo' => 'Martes 2x1', 'descripcion' => 'Lleva dos entradas al precio de una todos los martes.', 'imagen' => 'https://images.unsplash.com/photo-1585647347483-22b66260dfff?w=800', 'descuento' => '2x1', 'vigencia' => '31 Dic', 'codigo' => 'CINE2X1'],
                ['id' => 2, 'titulo' => 'Combo Familiar', 'descripcion' => 'Canchita gigante + 2 bebidas con 30% de descuento.', 'imagen' => 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=800', 'descuento' => '30% OFF', 'vigencia' => '15 Jul'],
                ['id' => 3, 'titulo' => 'Descuento Estudiantes', 'descripcion' => 'Presenta tu carnet universitario y obtén 20% en cualquier función.', 'imagen' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800', 'descuento' => '20% OFF', 'codigo' => 'JULIOS20'],
                ['id' => 4, 'titulo' => 'Miércoles de Dulcería', 'descripcion' => '15% de descuento en toda la dulcería.', 'imagen' => 'https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?w=800', 'descuento' => '15% OFF', 'vigencia' => '31 Dic'],
            ],
        ]);
    }
}
