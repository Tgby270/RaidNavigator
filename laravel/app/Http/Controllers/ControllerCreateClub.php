<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;

class ControllerCreateClub extends Controller
{
    
    public function create()
    {
        return inertia('Club/CreateClub');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CLU_NOM' => 'required|string|max:255',
            'CLU_ADRESSE' => 'required|string|max:255',
            'CLU_CODE_POSTAL' => 'required|string|max:20',
            'CLU_VILLE' => 'required|string|max:255',
            'USE_ID' => 'required|integer|exists:users,id',
        ]);

        Club::create($validated);

        return redirect()->route('admin-dashboard')->with('success', 'Club créé avec succès !');
    }

    
}