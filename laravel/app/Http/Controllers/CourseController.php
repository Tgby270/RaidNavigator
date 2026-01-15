<?php

namespace App\Http\Controllers;

use App\Models\COURSE;
use App\Models\User;
use App\Models\TARIFER;
use App\Models\RAIDS;
use App\Models\EQUIPE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function create($raidId = null)
    {
        $users = User::all();
        $raid = null;
        
        // Si raidId est fourni, récupérer les informations du raid
        if ($raidId) {
            $raid = \App\Models\RAIDS::find($raidId);
            if (!$raid) {
                return redirect()->back()->withErrors(['error' => 'Le RAID spécifié n\'existe pas.']);
            }
        }
        
        return Inertia::render('Course/CreateCourse', [
            'users' => $users,
            'raid_id' => $raidId,
            'raid' => $raid
        ]);
    }

    public function store(Request $request, $raidId = null)
    {
        Log::info('Course creation attempt', ['data' => $request->all()]);

        // Accept raid_id either as route parameter or as form field
        $raidId = $raidId ?? $request->input('raid_id');

        try {
            // Basic validation rules
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'duration' => 'required|integer',
                'dateB' => 'required|date',
                'hourB' => 'required',
                'dateE' => 'required|date',
                'hourE' => 'required',
                'participantsMin' => 'required|integer|min:1',
                'participantsMax' => 'required|integer|min:1',
                'participantNbByTeam' => 'required|integer|min:1',
                'teamMin' => 'required|integer|min:1',
                'teamMax' => 'required|integer|min:1',
                'use_id' => 'required|exists:sae_users,USE_ID',
                'mealPrice' => 'nullable|numeric',
                'priceUnder18' => 'required|numeric',
                'priceOver18' => 'required|numeric',
                'discount' => 'nullable|integer',
                'difficulte' => 'required|string|max:255',
            ]);

            // Post-validation: custom checks for datetime consistency and future dates
            $validator->after(function ($v) use ($request, $raidId) {
                try {
                    $dateHeureDepart = \Carbon\Carbon::parse($request->input('dateB') . ' ' . $request->input('hourB'));
                    $dateHeureFin = \Carbon\Carbon::parse($request->input('dateE') . ' ' . $request->input('hourE'));
                } catch (\Exception $e) {
                    $v->errors()->add('dateB', 'Format de date/heure invalide.');
                    return;
                }

                if ($dateHeureDepart->lte(now())) {
                    $v->errors()->add('dateB', 'La date et l\'heure de départ doivent être dans le futur.');
                }

                if ($dateHeureFin->lte(now())) {
                    $v->errors()->add('dateE', 'La date et l\'heure de fin doivent être dans le futur.');
                }

                if ($dateHeureDepart->gte($dateHeureFin)) {
                    $v->errors()->add('dateB', 'La date et heure de départ doivent être avant la date et heure de fin.');
                }

                // Vérifier que les dates de la course sont dans les dates du raid
                if ($raidId) {
                    $raid = RAIDS::find($raidId);
                    if ($raid) {
                        try {
                            $raidDateDebut = \Carbon\Carbon::parse($raid->raid_date_debut);
                            $raidDateFin = \Carbon\Carbon::parse($raid->raid_date_fin)->endOfDay();
                            
                            // Vérifier que la date de départ de la course est >= date de début du raid
                            if ($dateHeureDepart->lt($raidDateDebut)) {
                                $v->errors()->add('dateB', 'La date de départ de la course doit être après ou égale à la date de début du raid (' . $raidDateDebut->format('d/m/Y') . ').');
                            }
                            
                            // Vérifier que la date de fin de la course est <= date de fin du raid
                            if ($dateHeureFin->gt($raidDateFin)) {
                                $v->errors()->add('dateE', 'La date de fin de la course doit être avant ou égale à la date de fin du raid (' . $raidDateFin->format('d/m/Y') . ').');
                            }
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de la validation des dates du raid', ['error' => $e->getMessage()]);
                        }
                    }
                }

                // numeric comparisons already enforced for integer, but we need min < max
                $participantsMin = intval($request->input('participantsMin'));
                $participantsMax = intval($request->input('participantsMax'));
                // Allow equality (min == max) but disallow min > max
                $participantsMin = intval($request->input('participantsMin'));
                $participantsMax = intval($request->input('participantsMax'));
                if ($participantsMin > $participantsMax) {
                    $v->errors()->add('participantsMin', 'Le nombre minimum de participants doit être inférieur ou égal au nombre maximum.');
                }

                $teamMin = intval($request->input('teamMin'));
                $teamMax = intval($request->input('teamMax'));
                if ($teamMin > $teamMax) {
                    $v->errors()->add('teamMin', 'Le nombre minimum d\'équipes doit être inférieur ou égal au nombre maximum.');
                }

                // Removed arbitrary '< 3' constraint; minimum values are only validated against their respective maximums now.
            });

            $validated = $validator->validate();

            Log::info('Validation passed', ['validated' => $validated]);

            // If a 'genre' field was sent (some frontends send type and genre separately), merge them into type
            $genreFromRequest = $request->input('genre', null);
            if (!is_null($genreFromRequest)) {
                $baseType = explode('-', $validated['type'])[0];
                $validated['type'] = $baseType . '-' . strtolower(trim($genreFromRequest));
                Log::info('Merged type and genre into', ['type' => $validated['type']]);
            }

            // Combiner date et heure
            $dateHeureDepart = $validated['dateB'] . ' ' . $validated['hourB'];
            $dateHeureFin = $validated['dateE'] . ' ' . $validated['hourE'];
            
            // Vérifier si le RAID existe
            $raid = RAIDS::find($raidId);
            if (!$raid) {
                Log::error('RAID not found', ['raid_id' => $raidId]);
                return redirect()->back()->withErrors(['error' => 'Le RAID spécifié n\'existe pas. Veuillez d\'abord créer un RAID.']);
            }

            $lastCourse = COURSE::where('RAID_ID', $raidId)->orderBy('CRS_ID', 'desc')->first();
            $crsId = $lastCourse ? $lastCourse->CRS_ID + 1 : 1;

            Log::info('Creating course', ['raid_id' => $raidId, 'crs_id' => $crsId]);

            $course = COURSE::create([
                'RAID_ID' => $raidId,
                'CRS_ID' => $crsId,
                'USE_ID' => $validated['use_id'],
                'CRS_NOM' => $validated['title'],
                'CRS_TYPE' => $validated['type'],
                'CRS_DUREE' => $validated['duration'],
                'CRS_REDUC_LICENCIE' => $validated['discount'],
                'CRS_DATE_HEURE_DEPART' => $dateHeureDepart,
                'CRS_DATE_HEURE_FIN' => $dateHeureFin,
                'CRS_MIN_PARTICIPANTS' => $validated['participantsMin'],
                'CRS_MAX_PARTICIPANTS' => $validated['participantsMax'],
                'CRS_MAX_PARTICIPANTS_EQUIPE' => $validated['participantNbByTeam'],
                'CRS_NB_EQUIPE_MIN' => $validated['teamMin'],
                'CRS_NB_EQUIPE_MAX' => $validated['teamMax'],
                'CRS_PRIX_REPAS' => $validated['mealPrice'],
                'CRS_DIFFICULTE' => $validated['difficulte'],
            ]);


            $tarifer1 = TARIFER::create([
                'RAID_ID' => $raidId,
                'CRS_ID'=> $crsId,
                'AGE_ID'=> '1',
                'PRIX'=> $validated['priceUnder18'],
            ]);

            $tarifer2 = TARIFER::create([
                'RAID_ID' => $raidId,
                'CRS_ID'=> $crsId,
                'AGE_ID'=> '2',
                'PRIX'=> $validated['priceOver18'],
            ]);

            Log::info('Course created successfully', ['course' => $course]);

            // Redirection vers la page RaidDetail
            return Inertia::location('/raid/manage/' . $raidId);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Course creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création de la course: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request)
    {
        Log::info('Course update attempt', ['data' => $request->all()]);

        try {
            // Use Validator to add post-validation checks similar to store()
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'raid_id' => 'required|integer|exists:sae_raids,RAID_ID',
                'course_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'duration' => 'required|integer',
                'dateB' => 'required|date',
                'hourB' => 'required',
                'dateE' => 'required|date',
                'hourE' => 'required',
                'participantsMin' => 'required|integer|min:1',
                'participantsMax' => 'required|integer|min:1',
                'teamMin' => 'required|integer|min:1',
                'teamMax' => 'required|integer|min:1',
                'use_id' => 'required|exists:sae_users,USE_ID',
                'mealPrice' => 'nullable|numeric',
                'priceUnder18' => 'nullable|numeric|min:0',
                'priceOver18' => 'nullable|numeric|min:0',
                'discount' => 'nullable|integer',
                'difficulte' => 'nullable|string|max:255',
            ]);

            $validator->after(function ($v) use ($request) {
                try {
                    $dateHeureDepart = \Carbon\Carbon::parse($request->input('dateB') . ' ' . $request->input('hourB'));
                    $dateHeureFin = \Carbon\Carbon::parse($request->input('dateE') . ' ' . $request->input('hourE'));
                } catch (\Exception $e) {
                    $v->errors()->add('dateB', 'Format de date/heure invalide.');
                    return;
                }

                if ($dateHeureDepart->lte(now())) {
                    $v->errors()->add('dateB', 'La date et l\'heure de départ doivent être dans le futur.');
                }

                if ($dateHeureFin->lte(now())) {
                    $v->errors()->add('dateE', 'La date et l\'heure de fin doivent être dans le futur.');
                }

                if ($dateHeureDepart->gte($dateHeureFin)) {
                    $v->errors()->add('dateB', 'La date et heure de départ doivent être avant la date et heure de fin.');
                }

                $participantsMin = intval($request->input('participantsMin'));
                $participantsMax = intval($request->input('participantsMax'));
                if ($participantsMin > $participantsMax) {
                    $v->errors()->add('participantsMin', 'Le nombre minimum de participants doit être inférieur ou égal au nombre maximum.');
                }

                $teamMin = intval($request->input('teamMin'));
                $teamMax = intval($request->input('teamMax'));
                if ($teamMin > $teamMax) {
                    $v->errors()->add('teamMin', 'Le nombre minimum d\'équipes doit être inférieur ou égal au nombre maximum.');
                }

                // Removed arbitrary '< 3' constraint; minimum values are only validated against their respective maximums now.
            });

            $validated = $validator->validate();

            Log::info('Validation passed', ['validated' => $validated]);

            // If a 'genre' field was sent (some frontends send type and genre separately), merge them into type
            $genreFromRequest = $request->input('genre', null);
            if (!is_null($genreFromRequest)) {
                $baseType = explode('-', $validated['type'])[0];
                $validated['type'] = $baseType . '-' . strtolower(trim($genreFromRequest));
                Log::info('Merged type and genre into', ['type' => $validated['type']]);
            }

            // Combiner date et heure
            $dateHeureDepart = $validated['dateB'] . ' ' . $validated['hourB'];
            $dateHeureFin = $validated['dateE'] . ' ' . $validated['hourE'];

            $raidId = $validated['raid_id'];
            $crsId = $validated['course_id'];

            // Vérifier si le RAID existe
            $raid = \App\Models\RAIDS::find($raidId);
            if (!$raid) {
                Log::error('RAID not found', ['raid_id' => $raidId]);
                return redirect()->back()->withErrors(['error' => 'Le RAID spécifié n\'existe pas.']);
            }

            // Rechercher la course existante
            $course = COURSE::where('RAID_ID', $raidId)->where('CRS_ID', $crsId)->first();
            if (!$course) {
                Log::error('Course not found', ['raid_id' => $raidId, 'crs_id' => $crsId]);
                return redirect()->back()->withErrors(['error' => 'La course spécifiée est introuvable.']);
            }

            // Build the values to update
            $updateFields = [
                'USE_ID' => $validated['use_id'],
                'CRS_NOM' => $validated['title'],
                'CRS_TYPE' => $validated['type'],
                'CRS_DUREE' => $validated['duration'],
                'CRS_REDUC_LICENCIE' => $validated['discount'],
                'CRS_DATE_HEURE_DEPART' => $dateHeureDepart,
                'CRS_DATE_HEURE_FIN' => $dateHeureFin,
                'CRS_MIN_PARTICIPANTS' => $validated['participantsMin'],
                'CRS_MAX_PARTICIPANTS' => $validated['participantsMax'],
                'CRS_NB_EQUIPE_MIN' => $validated['teamMin'],
                'CRS_NB_EQUIPE_MAX' => $validated['teamMax'],
                'CRS_MAX_PARTICIPANTS_EQUIPE' => $validated['participantsMax'],
                'CRS_PRIX_REPAS' => $validated['mealPrice'],
                'CRS_DIFFICULTE' => $validated['difficulte'],
            ];

            // Use a query update to avoid Eloquent save() issues with composite primary keys
            $affected = COURSE::where('RAID_ID', $raidId)->where('CRS_ID', $crsId)->update($updateFields);
            Log::info('Course update query', ['affected' => $affected, 'updateFields' => $updateFields]);

            $course = COURSE::where('RAID_ID', $raidId)->where('CRS_ID', $crsId)->first();

            Log::info('Course updated successfully', ['course' => $course]);

            // Update tariffs if prices were provided
            \Illuminate\Support\Facades\DB::transaction(function() use ($request, $raidId, $crsId) {
                // prix pour <18 (AGE_ID category min/max: null / 17)
                if ($request->filled('priceUnder18')) {
                    $priceUnder = $request->input('priceUnder18');
                    $ageUnder = \App\Models\AgeCategory::firstOrCreate([
                        'AGE_MIN' => 0,
                        'AGE_MAX' => 17,
                    ]);

                    \Illuminate\Support\Facades\DB::table('sae_tarifer')->updateOrInsert(
                        ['RAID_ID' => $raidId, 'CRS_ID' => $crsId, 'AGE_ID' => $ageUnder->AGE_ID],
                        ['PRIX' => $priceUnder, 'updated_at' => now(), 'created_at' => now()]
                    );

                    Log::info('Updated tariff for under 18', ['raid' => $raidId, 'crs' => $crsId, 'age_id' => $ageUnder->AGE_ID, 'price' => $priceUnder]);
                }

                // prix pour >18 (AGE_ID category min/max: 18 / null)
                if ($request->filled('priceOver18')) {
                    $priceOver = $request->input('priceOver18');
                    $ageOver = \App\Models\AgeCategory::firstOrCreate([
                        'AGE_MIN' => 18,
                        'AGE_MAX' => 99,
                    ]);

                    \Illuminate\Support\Facades\DB::table('sae_tarifer')->updateOrInsert(
                        ['RAID_ID' => $raidId, 'CRS_ID' => $crsId, 'AGE_ID' => $ageOver->AGE_ID],
                        ['PRIX' => $priceOver, 'updated_at' => now(), 'created_at' => now()]
                    );

                    Log::info('Updated tariff for over 18', ['raid' => $raidId, 'crs' => $crsId, 'age_id' => $ageOver->AGE_ID, 'price' => $priceOver]);
                }
            });

            // Redirect back to the course edit page so updated prices are visible
            return Inertia::location('/raid/manage/' . $raidId );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Course update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la modification de la course: ' . $e->getMessage()])->withInput();
        }
    }

    public static function getter($raidID)
    {
        $controller = new self();
        $courses = $controller->getCoursesParRaid($raidID);
        return $courses;
    }
    public function getCoursesParRaid($raidId)
    {
        $courses = COURSE::where('RAID_ID', $raidId)->get();
        foreach ($courses as $course) {
            $counters = $this->getNbEquipesAndNbOfMembersInscritesParCourse($raidId, $course->CRS_ID);
            $course->nb_equipes = $counters[0];
            $course->nb_members = $counters[1];
        }
        return $courses;
    }

    public function getNbEquipesAndNbOfMembersInscritesParCourse($raidId, $courseId)
    {
        $count = EQUIPE::where('sae_equipe.CRS_ID', $courseId)->count();
        $countMembers = EQUIPE::where('sae_equipe.CRS_ID', $courseId)
            ->join('sae_appartenir', 'sae_equipe.EQU_ID', '=', 'sae_appartenir.EQU_ID')
            ->count();
        return [$count, $countMembers];
    }
}

?>