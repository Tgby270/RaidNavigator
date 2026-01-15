<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\RAIDS as Raid;
use App\Models\COURSE;
use App\Models\AgeCategory;
use App\Models\EQUIPE;

/**
 * This Controller regroups all the functions necessary to the use of the API
 */
class APIController extends Controller
{
    /**
     * Function to test if the API is functionning properly
     * @return \Illuminate\Http\JsonResponse
     */
    public function connectionTest()
    {
        return response()->json(['status' => 'API is working']);
    }

    /* -------------------------------------------------------------------------- */
    /*                                 USER DATAS                                 */
    /* -------------------------------------------------------------------------- */

    /*
        We have chosen to reduce the user datas function for the API to it's minimum in order to respect the RGPD
        So you won't find any function to get any personal datas about the users of the application.
     */

    /**
     * Function to get the number of users registered in the application
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfUsers()
    {
        $numberOfUsers = User::count();
        return response()->json(['number_of_users' => $numberOfUsers]);
    }

    /**
     * Function to get the number of users who possess a licence to a club
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfUnlicensedUsers()
    {
        $numberOfUnlicensedUsers = User::whereNull('USE_NUM_PPS')->count();
        return response()->json(['number_of_unlicensed_users' => $numberOfUnlicensedUsers]);
    }

    /**
     * Function to get the number of users who possess a licence to a club
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfLicensedUsers()
    {
        $numberOfLicensedUsers = User::whereNotNull('USE_NUM_LICENCIE')->count();
        return response()->json(['number_of_licensed_users' => $numberOfLicensedUsers]);
    }

    /**
     * Function to get the number of users per city
     * For privacy purposes, only the city names and the number of users are returned, and only if there is more than 10 users in that city
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfUsersPerCity()
    {
        $usersPerCity = User::select('USE_VILLE', \DB::raw('count(*) as total'))
            ->groupBy('USE_VILLE')
            ->havingRaw('count(*) > 10')
            ->get();
        return response()->json(['users_per_city' => $usersPerCity]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                 CLUB DATAS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Function to get all the clubs registered in the application and their informations
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllClubs()
    {
        $clubs = Club::all()->makeHidden(['created_at', 'updated_at']);
        if ($clubs->isEmpty()) {
            return response()->json(['clubs' => 'No clubs found'], 404);
        }
        return response()->json(['clubs' => $clubs]);
    }

    /**
     * Function to get a club by its ID
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClubById($id)
    {
        $club = Club::find($id);
        if ($club) {
            return response()->json(['club' => $club->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Club not found'], 404);
        }
    }

    /**
     * Function to get statistics about all the clubs
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClubStats()
    {
        $totalClubs = Club::count();
        $clubsByCity = Club::select('CLB_VILLE', \DB::raw('count(*) as total'))
            ->groupBy('CLB_VILLE')
            ->get();

        return response()->json([
            'total_clubs' => $totalClubs,
            'clubs_by_city' => $clubsByCity
        ]);
    }

    /**
     * Function to get the total numbers of clubs in the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNbClubs()
    {
        $totalClubs = Club::count();
        return response()->json(['total_clubs' => $totalClubs]);
    }

    /**
     * Function to get the number of clubs per city
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNbClubsPerCity()
    {
        $clubsPerCity = Club::select('CLB_VILLE', \DB::raw('count(*) as total'))
            ->groupBy('CLB_VILLE')
            ->get();

        return response()->json(['clubs_per_city' => $clubsPerCity]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                 RAID DATAS                                 */
    /* -------------------------------------------------------------------------- */
    /**
     * All images are acessible with the following route : /Images/Card/ + name of the image which you can find in the API call
     */

    /**
     * Function to get all the raids registered in the application and their informations
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRaids()
    {
        $raids = Raid::all()->makeHidden(['created_at', 'updated_at']);
        if ($raids->isEmpty()) {
            return response()->json(['raids' => 'No raids found'], 404);
        }
        return response()->json(['raids' => $raids]);
    }

    /**
     * Function to get a raid by its ID
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRaidById($id)
    {
        $raid = Raid::find($id);
        if ($raid) {
            return response()->json(['raid' => $raid->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Raid not found'], 404);
        }
    }

    /**
     * Function to get a raid by its name
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRaidByName($name)
    {
        $raid = Raid::where('RAID_NOM', $name)->first();
        if ($raid) {
            return response()->json(['raid' => $raid->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Raid not found'], 404);
        }
    }

    /**
     * Function to get upcoming raids
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingRaids()
    {
        $upcomingRaids = Raid::where('RAID_DATE_DEBUT', '>=', now())
            ->orderBy('RAID_DATE_DEBUT', 'asc')
            ->get()
            ->makeHidden(['created_at', 'updated_at']);

        if ($upcomingRaids->isEmpty()) {
            return response()->json(['error' => 'No upcoming raids found'], 404);
        }

        return response()->json(['upcoming_raids' => $upcomingRaids]);
    }

    /**
     * Function to get statistics about all the raids
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRaidStats()
    {
        $totalRaids = Raid::count();

        $raidsByCity = Raid::selectRaw(
            "SUBSTRING(raid_lieu, LENGTH(raid_lieu) - LOCATE('-', REVERSE(raid_lieu)) + 2) AS city, COUNT(*) AS total"
        )
            ->groupBy('city')
            ->orderBy('city')
            ->get();

        if ($totalRaids === 0 && $raidsByCity->isEmpty()) {
            return response()->json(['error' => 'No raids found'], 404);
        }

        return response()->json([
            'total_raids' => $totalRaids,
            'raids_by_city' => $raidsByCity,
        ]);
    }

    /**
     * Function to get the total numbers of raids in the database
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNbRaids()
    {
        $totalRaids = Raid::count();
        if ($totalRaids === 0) {
            return response()->json(['error' => 'No raids found'], 404);
        }
        return response()->json(['total_raids' => $totalRaids]);
    }

    /**
     * Function to get races associated with a raid by the raid ID
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRacesByRaidId($id)
    {
        $raid = Raid::find($id);
        if (!$raid) {
            return response()->json(['error' => 'Raid not found'], 404);
        }

        $races = COURSE::where('RAID_ID', $id)->get()->makeHidden(['created_at', 'updated_at']);
        if ($races->isEmpty()) {
            return response()->json(['error' => 'No races found for this raid'], 404);
        }
        return response()->json(['races' => $races]);
    }

    /**
     * Function to get races associated with a raid by the raid name
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRacesByRaidName($name)
    {
        $raid = Raid::where('RAID_NOM', $name)->first();
        if (!$raid) {
            return response()->json(['error' => 'Raid not found'], 404);
        }
        $races = COURSE::where('RAID_ID', $raid->RAID_ID)->get()->makeHidden(['created_at', 'updated_at']);
        if ($races->isEmpty()) {
            return response()->json(['error' => 'No races found for this raid'], 404);
        }
        return response()->json(['races' => $races]);
    }



    /* -------------------------------------------------------------------------- */
    /*                                 RACES DATA                                 */
    /* -------------------------------------------------------------------------- */

    public function getAllRaces()
    {
        $races = COURSE::all()->makeHidden(['created_at', 'updated_at']);
        if ($races->isEmpty()) {
            return response()->json(['races' => 'No races found'], 404);
        }
        return response()->json(['races' => $races]);
    }

    /**
     * Function to get a race by its ID
     * @param int $raidId the ID of the raid
     * @param int $id the ID of the race
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRaceById($raidId, $id)
    {
        $race = COURSE::where('RAID_ID', $raidId)
            ->where('CRS_ID', $id)
            ->first();
        if ($race) {
            return response()->json(['race' => $race->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Race not found'], 404);
        }
    }

    /**
     * Function to get a race by its name
     * @param string $name the name of the race
     * @return \Illuminate\Http\JsonResponse
     */

    public function getRaceByName($name)
    {
        $race = COURSE::whereRaw('LOWER(TRIM(CRS_NOM)) = LOWER(TRIM(?))', [$name])->first();
        if ($race) {
            return response()->json(['race' => $race->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Race not found'], 404);
        }
    }

    /**
     * Function to get races by their type
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRacesByType($type)
    {
        $races = COURSE::where('CRS_TYPE', $type)->get()->makeHidden(['created_at', 'updated_at']);
        if ($races->isEmpty()) {
            return response()->json(['error' => 'No races found for this type'], 404);
        }
        return response()->json(['races' => $races]);
    }

    /* -------------------------------------------------------------------------- */
    /*                             AGE CATEGORY DATAS                             */
    /* -------------------------------------------------------------------------- */

    /**
     * Function to get all age categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgeCategories()
    {
        $ageCategories = AgeCategory::all()->makeHidden(['created_at', 'updated_at']);
        if ($ageCategories->isEmpty()) {
            return response()->json(['age_categories' => 'No age categories found'], 404);
        }
        return response()->json(['age_categories' => $ageCategories]);
    }

    /**
     * Function to get an age category by its ID
     * @param int $id the ID of the age category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgeCategoryById($id)
    {
        $ageCategory = AgeCategory::find($id);
        if ($ageCategory) {
            return response()->json(['age_category' => $ageCategory->makeHidden(['created_at', 'updated_at'])]);
        } else {
            return response()->json(['error' => 'Age category not found'], 404);
        }
    }

    /**
     * Function to get races associated with an age category by the age category ID
     * @param int $id the ID of the age category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCoursesByAgeCategoryId($id)
    {
        $ageCategory = AgeCategory::find($id);
        if (!$ageCategory) {
            return response()->json(['error' => 'Age category not found'], 404);
        }

        $races = \DB::select("SELECT * FROM sae_course JOIN sae_tarifer USING (CRS_ID, RAID_ID) JOIN sae_cat_age USING (AGE_ID) WHERE AGE_ID = ?", [$id]);
        return response()->json(['races' => $races]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                 TEAM DATAS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Function to get all teams
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTeams()
    {
        $teams = EQUIPE::all()->makeHidden(['created_at', 'updated_at', 'EQU_IMAGE']);
        if ($teams->isEmpty()) {
            return response()->json(['teams' => 'No teams found'], 404);
        }
        return response()->json(['teams' => $teams]);
    }

    /**
     * Function to get a team by its ID
     * @param int $id the ID of the team
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeamById($raid_id, $crs_id, $id)
    {
        $team = EQUIPE::where('RAID_ID', $raid_id)
            ->where('CRS_ID', $crs_id)
            ->where('EQU_ID', $id)
            ->first();
        if ($team) {
            return response()->json(['team' => $team->makeHidden(['created_at', 'updated_at', 'EQU_IMAGE'])]);
        } else {
            return response()->json(['error' => 'Team not found'], 404);
        }
    }

    /**
     * Function to get teams associated with a race by the race and raid ID
     * @param int $raid_id the ID of the raid
     * @param int $crs_id the ID of the course
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeamsByRaceId($raid_id, $crs_id)
    {
        $teams = EQUIPE::where('RAID_ID', $raid_id)
            ->where('CRS_ID', $crs_id)
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'EQU_IMAGE']);
        if ($teams->isEmpty()) {
            return response()->json(['error' => 'No teams found for this race'], 404);
        }
        return response()->json(['teams' => $teams]);
    }

    /**
     * Function to get all payed teams
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPayedTeams()
    {
        $teams = EQUIPE::whereNotNull('EQU_EST_PAYEE')
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'EQU_IMAGE']);
        if ($teams->isEmpty()) {
            return response()->json(['error' => 'No payed teams found'], 404);
        }
        return response()->json(['teams' => $teams]);
    }

    /**
     * Function to get all unpayed teams
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUnpayedTeams()
    {
        $teams = EQUIPE::whereNull('EQU_EST_PAYEE')
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'EQU_IMAGE']);
        if ($teams->isEmpty()) {
            return response()->json(['error' => 'No unpayed teams found'], 404);
        }
        return response()->json(['teams' => $teams]);
    }

    /**
     * Function to get statistics about teams payment status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNumberOfTeamsByPaymentStatus()
    {
        $payedTeamsCount = EQUIPE::whereNotNull('EQU_EST_PAYEE')->count();
        $unpayedTeamsCount = EQUIPE::whereNull('EQU_EST_PAYEE')->count();

        return response()->json([
            'payed_teams' => $payedTeamsCount,
            'unpayed_teams' => $unpayedTeamsCount
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                               API WRITE DATA                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Function to add a new user
     * @param Request $request the request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'USE_NOM' => 'required|string|max:255',
            'USE_PRENOM' => 'required|string|max:255',
            'USE_EMAIL' => 'required|string|email|max:255|unique:sae_user,USE_EMAIL',
            'PASSWORD' => 'required|string|min:6',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $user = User::create($validatedData);

        if (!$user) {
            return response()->json(['error' => 'User could not be created'], 500);
        }

        if (!$user->wasRecentlyCreated) {
            return response()->json(['error' => 'User already exists'], 409);
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Function to add a new raid
     * @param Request $request the request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRaid(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'RAID_NOM' => 'required|string|max:255',
            'RAID_DATE_DEBUT' => 'required|date',
            'RAID_DATE_FIN' => 'required|date|after_or_equal:RAID_DATE_DEBUT',
            'RAID_LIEU' => 'required|string|max:255', /* FORMAT : adress-postal_code-city  */
            'RAID_CONTACT' => 'required|string|max:255',
            'DATE_FIN_INSCRIPTION' => 'required|date|before:RAID_DATE_DEBUT',
            'DATE_DEBUT_INSCRIPTION' => 'required|date|before:DATE_FIN_INSCRIPTION',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $raid = Raid::create($validatedData);

        if (!$raid) {
            return response()->json(['error' => 'Raid could not be created'], 500);
        }

        if (!$raid->wasRecentlyCreated) {
            return response()->json(['error' => 'Raid already exists'], 409);
        }

        return response()->json(['message' => 'Raid created successfully', 'raid' => $raid], 201);
    }

    /**
     * Function to add a new club
     * @param Request $request the request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function addClub(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'CLB_NOM' => 'required|string|max:255',
            'CLB_VILLE' => 'required|string|max:255',
            'CLB_CODE_POSTAL' => 'required|string|max:10',
            'CLB_ADRESSE' => 'required|string|max:255',
            'CLB_CONTACT' => 'required|string|max:255',
            'USE_ID' => 'required|integer|exists:sae_user,USE_ID',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $club = Club::create($validatedData);

        if (!$club) {
            return response()->json(['error' => 'Club could not be created'], 500);
        }

        if (!$club->wasRecentlyCreated) {
            return response()->json(['error' => 'Club already exists'], 409);
        }

        return response()->json(['message' => 'Club created successfully', 'club' => $club], 201);
    }

    /**
     * Function to add a new team
     * @param Request $request the request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeam(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'RAID_ID' => 'required|integer|exists:sae_raids,RAID_ID',
            'CRS_ID' => 'required|integer|exists:sae_course,CRS_ID',
            'USE_ID' => 'required|integer|exists:sae_user,USE_ID',
            'EQU_NOM' => 'required|string|max:255',
            'EQU_EST_PAYEE' => 'nullable|boolean',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $team = EQUIPE::create($validatedData);

        if (!$team) {
            return response()->json(['error' => 'Team could not be created'], 500);
        }

        if (!$team->wasRecentlyCreated) {
            return response()->json(['error' => 'Team already exists'], 409);
        }

        return response()->json(['message' => 'Team created successfully', 'team' => $team], 201);
    }

    /**
     * Function to add a new race
     * @param Request $request the request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRace(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'RAID_ID' => 'required|integer|exists:sae_raids,RAID_ID',
            'CRS_NOM' => 'required|string|max:255',
            'USE_ID' => 'required|integer|exists:sae_user,USE_ID',
            'CRS_TYPE' => 'required|string|max:100',
            'CRS_DUREE' => 'required|numeric|min:0',
            'CRS_MIN_PARTICIPANTS' => 'required|integer|min:1',
            'CRS_MAX_PARTICIPANTS' => 'required|integer|min:1',
            'CRS_NB_EQUIPES_MAX' => 'required|integer|min:1',
            'CRS_NB_EQUIPES_MIN' => 'required|integer|min:1',
            'CRS_MAX_PARTICIPANTS_PAR_EQUIPE' => 'required|integer',
            'CRS_PRIX_REPAS' => 'required|numeric|min:0',
            'CRS_DIFFICULTE' => 'required|string|max:100',
            'CRS_DATE_HEURE_DEPART' => 'required|date',
            'CRS_DATE_HEURE_FIN' => 'required|date|after_or_equal:CRS_DATE_HEURE_DEPART',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        $race = COURSE::create($validatedData);

        if (!$race) {
            return response()->json(['error' => 'Race could not be created'], 500);
        }
        if (!$race->wasRecentlyCreated) {
            return response()->json(['error' => 'Race already exists'], 409);
        }
        return response()->json(['message' => 'Race created successfully', 'race' => $race], 201);
    }
}
