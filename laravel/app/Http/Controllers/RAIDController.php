<?php
namespace App\Http\Controllers;
use App\Models\COURSE;
use App\Models\RAIDS;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RAIDController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
        $raids = RAIDS::orderBy('raid_date_debut', 'asc')->paginate(9);
        foreach ($raids as $raid) {
            $ageRange = $this->getAgeRange($raid->RAID_ID);
            $nb_courses = $this->getNbCoursesParRaid($raid->RAID_ID);
            $raid->nb_courses = $nb_courses;
            $raid->age_min = $ageRange['age_min'];
            $raid->age_max = $ageRange['age_max'];
        }
        return inertia('Overview', [
            'raids' => $raids,
        ]);
    }


    public function myRaids(Request $request)
    {
        $userId = $request->user()->USE_ID;

        $raids = RAIDS::join('sae_course', 'sae_course.RAID_ID', '=', 'sae_raids.RAID_ID')
            ->join('sae_appartenir', function ($join) {
                $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
                     ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID');
            })
            ->where('sae_appartenir.STU_ID', $userId)
            ->select('sae_raids.*')
            ->distinct()
            ->orderByDesc('sae_raids.raid_date_debut')
            ->paginate(9);

        return Inertia::render('My-RAIDs/MyRaids', [
            'raids' => $raids,
        ]);
    }

    public function myRaidDetail(Request $request, $raid_id)
    {
        $userId = $request->user()->USE_ID;
        $raid = RAIDS::findOrFail($raid_id);
        
        // Get only courses where the user participated
        $courses = COURSE::where('sae_course.RAID_ID', $raid_id)
            ->join('sae_appartenir', function($join) use ($userId) {
                $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
                    ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID')
                    ->where('sae_appartenir.STU_ID', '=', $userId);
            })
            ->select('sae_course.*')
            ->distinct()
            ->get();
        
        // Add results to each course
        $courses = $courses->map(function($course) {
            $results = \App\Models\EQUIPE::where('RAID_ID', $course->RAID_ID)
                ->where('CRS_ID', $course->CRS_ID)
                ->with(['resultats'])
                ->get()
                ->map(function($equipe) {
                    return [
                        'EQU_ID' => $equipe->EQU_ID,
                        'EQU_NOM' => $equipe->EQU_NOM,
                        'CATEGORIE' => $equipe->CATEGORIE ?? 'Mixte',
                        'RES_TEMPS' => $equipe->resultats->RES_TEMPS ?? null,
                        'RES_POINTS' => $equipe->resultats->RES_POINTS ?? 0,
                    ];
                })
                ->sortBy(function($result) {
                    return $result['RES_TEMPS'] ?? PHP_INT_MAX;
                })
                ->values();
            
            $course->results = $results;
            return $course;
        });
        
        return Inertia::render('My-RAIDs/MyRaidDetail', [
            'raid' => $raid,
            'courses' => $courses,
        ]);
    }
    

    public function getAgeRange($raidId)
    {
        $raid = RAIDS::find($raidId);
        if (!$raid) {
            return null;
        }

        $courses = COURSE::where('RAID_ID', $raidId)->get();
        $ageMin = null;
        $ageMax = null;

        foreach ($courses as $course) {
            $tariffs = \DB::table('sae_tarifer')
                ->where('CRS_ID', $course->CRS_ID)
                ->get();

            foreach ($tariffs as $tariff) {
                $ageCategory = \DB::table('sae_cat_age')
                    ->where('AGE_ID', $tariff->AGE_ID)
                    ->first();

                if ($ageCategory) {
                    if (is_null($ageMin) || $ageCategory->AGE_MIN < $ageMin) {
                        $ageMin = $ageCategory->AGE_MIN;
                    }
                    if (is_null($ageMax) || $ageCategory->AGE_MAX > $ageMax) {
                        $ageMax = $ageCategory->AGE_MAX;
                    }
                }
            }
        }

        return ['age_min' => $ageMin, 'age_max' => $ageMax];
    }

    public function threeNext()
    {
        $raids = RAIDS::where('raid_date_debut', '>=', now())->orderBy('raid_date_debut', 'asc')->take(3)->get();
        foreach ($raids as $raid) {
            $ageRange = $this->getAgeRange($raid->RAID_ID);
            $nb_courses = $this->getNbCoursesParRaid($raid->RAID_ID);
            $raid->nb_courses = $nb_courses;
            $raid->age_min = $ageRange['age_min'];
            $raid->age_max = $ageRange['age_max'];
        }
        return [
            'raids' => $raids,
        ];
    }

    public function getNbCoursesParRaid($raidId)
    {
        $raid = RAIDS::find($raidId);
        if (!$raid) {
            return null;
        }

        $nb = COURSE::where('RAID_ID', $raidId)->count();
        return $nb;
    }

    public function create()
    {
        $users = User::all();
        return Inertia::render('Raid/CreateRaid', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Tentative de création de raid', ['data' => $request->all()]);

        try {
            // Basic validation rules
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'dateInscriptionBegin' => 'required|date',
                'dateInscriptionEnd' => 'required|date',
                'dateBegin' => 'required|date',
                'dateEnd' => 'required|date',
                'location' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'use_id' => 'required|exists:sae_users,USE_ID',
                'numberOfRaces' => 'required|integer|min:1',
                'website' => 'nullable|url|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'club_id' => 'required|exists:sae_clubs,CLU_ID',
            ], [], [
                'title' => 'Nom du Raid',
                'dateInscriptionBegin' => 'Date de début des inscriptions',
                'dateInscriptionEnd' => 'Date de fin des inscriptions',
                'dateBegin' => 'Date de début du raid',
                'dateEnd' => 'Date de fin du raid',
                'location' => 'Lieu',
                'contact' => 'Moyen de contact',
                'use_id' => 'Responsable',
                'numberOfRaces' => 'Nombre de courses',
                'website' => 'Site web',
                'image' => 'Image',
                'club_id' => 'Club',
            ]);

            // Post-validation checks: future dates and ordering constraints
            $validator->after(function ($v) use ($request) {
                try {
                    $inscBegin = \Carbon\Carbon::parse($request->input('dateInscriptionBegin'));
                    $inscEnd = \Carbon\Carbon::parse($request->input('dateInscriptionEnd'));
                    $dateBegin = \Carbon\Carbon::parse($request->input('dateBegin'));
                    $dateEnd = \Carbon\Carbon::parse($request->input('dateEnd'));
                } catch (\Exception $e) {
                    $v->errors()->add('dateBegin', 'Format de date invalide.');
                    return;
                }

                // Dates must be today or in the future (accept today)
                $todayStart = now()->startOfDay();

                if ($inscBegin->lt($todayStart)) {
                    $v->errors()->add('dateInscriptionBegin', "La date de début des inscriptions doit être aujourd'hui ou dans le futur.");
                }
                if ($inscEnd->lt($todayStart)) {
                    $v->errors()->add('dateInscriptionEnd', "La date de fin des inscriptions doit être aujourd'hui ou dans le futur.");
                }
                if ($dateBegin->lt($todayStart)) {
                    $v->errors()->add('dateBegin', "La date de début du raid doit être aujourd'hui ou dans le futur.");
                }
                if ($dateEnd->lt($todayStart)) {
                    $v->errors()->add('dateEnd', "La date de fin du raid doit être aujourd'hui ou dans le futur.");
                }

                // Ordering constraints
                if ($inscBegin->gte($inscEnd)) {
                    $v->errors()->add('dateInscriptionBegin', 'La date de début des inscriptions doit être avant la date de fin des inscriptions.');
                }

                if ($inscEnd->gte($dateBegin)) {
                    $v->errors()->add('dateInscriptionEnd', 'La date de fin des inscriptions doit être avant la date de début du raid.');
                }

                if ($dateBegin->gte($dateEnd)) {
                    $v->errors()->add('dateBegin', 'La date de début du raid doit être avant la date de fin du raid.');
                }
            });

            $validated = $validator->validate();

            Log::info('Validation réussie', ['validated' => $validated]);

            // Gérer l'upload de l'image
            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // Générer un nom unique pour l'image
                $imageName = time() . '_' . $image->getClientOriginalName();
                // Déplacer l'image vers public/Images/Card
                $image->move(public_path('Images/Card'), $imageName);
                Log::info('Image téléchargée', ['image_name' => $imageName]);
            }

            // Vérifier si le club existe
            $club = \App\Models\CLUB::find($validated['club_id']);
            if (!$club) {
                Log::error('Club non trouvé', ['club_id' => $validated['club_id']]);
                return redirect()->back()->withErrors(['error' => 'Le club spécifié n\'existe pas.']);
            }

            $raid = RAIDS::create([
                'USE_ID' => $validated['use_id'],
                'CLU_ID' => $club->CLU_ID,
                'raid_nom' => $validated['title'],
                'raid_date_debut' => $validated['dateBegin'],
                'raid_date_fin' => $validated['dateEnd'],
                'raid_contact' => $validated['contact'],
                'raid_site_web' => $validated['website'],
                'raid_lieu' => $validated['location'],
                'raid_image' => $imageName,
                'date_fin_inscription' => $validated['dateInscriptionEnd'],
                'date_debut_inscription' => $validated['dateInscriptionBegin'],
                'nombre_de_courses' => $validated['numberOfRaces'],
            ]);

            Log::info('Raid créé avec succès', ['raid' => $raid]);

            return Inertia::location('/dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation échouée', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Création du raid échouée', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création du raid: ' . $e->getMessage()])->withInput();
        }
    }

    public function clubRaid($clu_id)
    {
        $raids = RAIDS::where('CLU_ID','=', $clu_id)->orderBy('raid_date_debut','asc')->get();
        return [
            'raids' => $raids,
        ];

    }

    public function countClubRaid($clu_id)
    {
        $raids = RAIDS::where('CLU_ID','=',$clu_id)->count();
        return $raids;
    }

    public function countRaidPerMonthPerClub($clu_id, $annee)
    {
         $raids = RAIDS::where('CLU_ID', $clu_id) ->
         whereYear('raid_date_debut', $annee)->
         get()->
         groupBy(function ($raid) { return $raid->raid_date_debut->format('m');}) ->
         map(function ($group) 
         { 
            return $group->count(); 
         }); 
         return $raids;

    }
}
?>