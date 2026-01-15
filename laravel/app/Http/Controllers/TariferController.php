<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TariferController extends Controller
{
    public function index($raid, $crs)
    {
        // TODO: Implémenter la logique pour afficher les tarifs
        return Inertia::render('Tarifs/Index', [
            'raid_id' => $raid,
            'course_id' => $crs
        ]);
    }

    public function store(Request $request, $raid, $crs)
    {
        // TODO: Implémenter la logique pour enregistrer les tarifs
        return redirect()->back()->with('success', 'Tarifs enregistrés avec succès!');
    }
}
