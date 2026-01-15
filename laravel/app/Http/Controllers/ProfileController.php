<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;      
use Illuminate\Validation\Rules\Password; 
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
   public function edit(Request $request)
    {
        // Provide the full user model to the page for form defaults
        return Inertia::render('user/UserAccount', [
            'user' => $request->user(),
        ]);
    }
   // ... imports

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('sae_users', 'USE_MAIL')->ignore($user->USE_ID, 'USE_ID'),
            ],
            'type' => ['required', 'in:licencie,adherent'],
            'numero_licence' => ['nullable', 'string', 'max:255'],
            'pps' => ['nullable', 'string', 'max:255'],
        ]);

        $user->USE_NOM = $validated['name'];
        $user->USE_PRENOM = $validated['prenom'];
        $user->USE_MAIL = $validated['email'];

        if ($validated['type'] === 'adherent') {
            $user->USE_NUM_PPS = $validated['pps'];
            $user->USE_NUM_LICENCIE = null; 

            DB::table('sae_clubs')
                ->where('USE_ID', $user->USE_ID)
                ->update(['USE_ID' => null]);

        } else {
            $user->USE_NUM_LICENCIE = $validated['numero_licence'];
            $user->USE_NUM_PPS = null;
            
        }

        $user->save();

        return Redirect::route('user.account')->with('success', 'Profil mis à jour avec succès.');
    }
    

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:6', Password::defaults()],
        ]);

        $request->user()->update([
            'USE_MDP' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}