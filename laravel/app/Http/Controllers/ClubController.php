<?php

namespace App\Http\Controllers;

use App\Models\CLUB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClubController extends Controller
{
    public function create()
    {
        $users = User::all();
        Log::info('CreateClub page accessed', ['users_count' => $users->count()]);
        
        return Inertia::render('Club/CreateClub', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Club creation attempt', ['data' => $request->all()]);

        try {
            $validated = $request->validate([
                'CLU_NOM' => 'required|string|max:255',
                'CLU_ADRESSE' => 'required|string|max:255',
                'CLU_CODE_POSTAL' => 'required|string|max:10',
                'CLU_VILLE' => 'required|string|max:255',
                'CLU_CONTACT' => 'required|string|max:20',
                'USE_ID' => 'required|exists:sae_users,USE_ID',
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            $club = CLUB::create($validated);

            Log::info('Club created successfully', ['club' => $club]);

            // Redirection vers le dashboard admin avec message de succès
            return redirect()->route('dashboard')
                ->with('success', 'Club créé avec succès !');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Club creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création du club: ' . $e->getMessage()])->withInput();
        }
    }

    // Edit an existing club
    public function edit($id)
    {
        $users = User::all();
        $club = CLUB::findOrFail($id);

        // Determine whether the current user can edit; allow viewing for others
        $canEdit = Auth::id() === $club->USE_ID;
        if (! $canEdit) {
            Log::warning('Unauthorized club edit attempt', ['club_id' => $id, 'auth_id' => Auth::id()]);
        }

        Log::info('EditClub page accessed', ['club_id' => $id, 'canEdit' => $canEdit]);
        return Inertia::render('Club/ModificationClub', [
            'users' => $users,
            'club' => $club,
            'canEdit' => $canEdit
        ]);
    }

    // Update an existing club
    public function update(Request $request, $id)
    {
        Log::info('Club update attempt', ['id' => $id, 'data' => $request->all()]);

        try {
            $validated = $request->validate([
                'CLU_NOM' => 'required|string|max:255',
                'CLU_ADRESSE' => 'required|string|max:255',
                'CLU_CODE_POSTAL' => 'required|string|max:10',
                'CLU_VILLE' => 'required|string|max:255',
                'CLU_CONTACT' => 'required|string|max:20',
                'USE_ID' => 'required|exists:sae_users,USE_ID',
            ]);

            $club = CLUB::findOrFail($id);
            $club->update($validated);

            Log::info('Club updated successfully', ['club' => $club]);

            return Inertia::location('/dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Club update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour du club: ' . $e->getMessage()])->withInput();
        }
    }
}
