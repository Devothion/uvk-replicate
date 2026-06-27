<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * NewsletterController — Suscripción al boletín desde el footer.
 */
class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // En real: Newsletter::firstOrCreate(['email' => $request->email]);

        return back()->with('success', '¡Gracias por suscribirte al boletín de CINEJULIOS!');
    }
}
