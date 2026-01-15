<?php
namespace App\Http\Controllers;

use App\Models\ADHERER;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sae_users,USE_MAIL',
            'password' => 'required|string|min:6|confirmed',
            'date_naissance' => 'required|date|before:today',
            'adresse' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
        ]);

        //create the user in the table sae_users
        $user = User::create([
            'USE_NOM' => $request->input('family_name'),
            'USE_PRENOM' => $request->input('name'),
            'USE_MAIL' => $request->input('email'),
            'USE_MDP' => bcrypt($request->input('password')),
            'USE_DATE_NAISSANCE' => $request->input('date_naissance'),
            'USE_ADRESSE' => $request->input('adresse', ''),
            'USE_TELEPHONE' => $request->input('phone_number', ''),
            'USE_CODE_POSTAL' => $request->input('postal_code', ''),
            'USE_VILLE' => $request->input('city', ''),
            'USE_NUM_LICENCIE' => $request->input('license', null),
        ]);

        if ($request->input('club_id') == 'Sélectionnez un club') {
            ADHERER::create([
                'USE_ID' => $user->USE_ID,
                'CLU_ID' => $request->input('club_id'),
            ]);
        }

        /*Connect the user after registration*/
        Auth::login($user);

        return redirect('/test')->with('status', 'Registration successful! Please log in.');
    }

    public function showRegistrationForm()
    {
        $clubs = \App\Models\CLUB::select('CLU_ID', 'CLU_NOM')->orderBy('CLU_NOM')->get();
        return Inertia::render('Auth/Register', [
            'clubs' => $clubs
        ]);
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'USE_MAIL' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Debug: Log user data
            $user = Auth::user();
            Log::info('Logged in user:', [
                'USE_ID' => $user->USE_ID,
                'USE_PRENOM' => $user->USE_PRENOM,
                'USE_NOM' => $user->USE_NOM,
                'USE_MAIL' => $user->USE_MAIL
            ]);

            return redirect()->intended('/test')->with('status', 'Login successful!');
        }

        return redirect()->intended('/')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Logged out successfully!');
    }
}
?>