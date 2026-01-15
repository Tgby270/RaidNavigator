<?php
namespace App\Http\Controllers;
use App\Models\RAIDS;
use App\Models\ADHERER;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class DashboardController extends Controller
{
    public function show(){
        // Get clubs owned by the current user (only club owner can create raids)
        $userId = Auth::id();
        $user = Auth::user();
        \Log::info('Dashboard: Current user', ['userId' => $userId, 'userName' => $user ? $user->USE_NOM . ' ' . $user->USE_PRENOM : 'Not found']);
        
        // Query club where USE_ID matches the authenticated user's ID
        $ownedClub = \App\Models\CLUB::where('USE_ID', $userId)->first();
        \Log::info('Dashboard: Club query result', [
            'found' => $ownedClub ? true : false,
            'clubName' => $ownedClub ? $ownedClub->CLU_NOM : null,
            'clubId' => $ownedClub ? $ownedClub->CLU_ID : null,
            'userId' => $userId
        ]);
        
        $clubId = $ownedClub ? $ownedClub->CLU_ID : null;
        
        $nb_raids = RAIDS::getRaidNumberByClub($clubId);

    //get the RAIDS that the user is responsible for
        $raids_responsible = RAIDS::where('USE_ID', Auth::id())->get();

    //get the COURSES that the user is responsible for
        $courses_responsible = \App\Models\COURSE::join('sae_raids', 'sae_course.RAID_ID', '=', 'sae_raids.RAID_ID')
            ->where('sae_course.USE_ID', Auth::id())
            ->select('sae_course.*', 'sae_raids.raid_image', 'sae_raids.raid_lieu', 'sae_raids.RAID_ID')
            ->get();


        $raids_club_user = RAIDS::join('sae_clubs', 'sae_clubs.CLU_ID', '=','sae_raids.CLU_ID')
        ->where('USE_ID', Auth::id())
        ->get();
           
        $number_of_user = User::count();



    return Inertia::render('Dashboard/Dashboard', [
        'nb_raids' => $nb_raids,
        'raids_responsible' => $raids_responsible,
        'courses_responsible' => $courses_responsible,
        'raids_club_user' => $raids_club_user,
        'number_of_user' => $number_of_user,
        'club_id' => $clubId,
        'club' => $ownedClub,
        'club_name' => $ownedClub ? $ownedClub->CLU_NOM : null
        ]);
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

    public function countRaidForAYear($year)
    {
        $nb_raids = RAIDS::whereYear('raid_date_debut',$year) -> count();
        return $nb_raids;
    }

      public function countRaidForAYearForAClub($year, $clu_id)
    {
        $nb_raids = RAIDS::where('CLU_ID','=',$clu_id)->whereYear('raid_date_debut',$year) -> count();
        return $nb_raids;
    }

    public function countUserForAClub($club_id)
    {
        $nb_user_club = ADHERER::where('CLU_ID',$club_id)->count();
        return $nb_user_club;
    }

    public function countRaidsByDepartement($year)
    {
        $raids = RAIDS::whereYear('raid_date_debut', $year)->get();

        $departementCounts = $raids->map(function ($raid) {
            $parts = explode('-', $raid->raid_lieu);
            return isset($parts[1]) ? substr(trim($parts[1]),0,2) : 'Inconnu';
        })->countBy();

        $tableau = $departementCounts->map(function ($count, $departement) {
            return [
                'departement' => $departement,
                'nombre'      => $count,
            ];
        })->values();

        return $tableau;
    }

     public function clubRaid($clu_id)
    {
        $raids = RAIDS::where('CLU_ID','=', $clu_id)->orderBy('raid_date_debut','asc')->get();
        return $raids;
    }

    public function countClubRaid($clu_id)
    {
        $raids = RAIDS::where('CLU_ID','=',$clu_id)->count();
        return $raids;
    }


    public function tauxCompletionClub($club_id)
    {
        $raids = RAIDS::where('clu_id', $club_id)
            ->where('raid_date_fin', '<=', now())
            ->with('sae_course')
            ->get();
        
        if ($raids->isEmpty()) {
            return 0;
        }
        
        $taux = $raids->map(function($raid) {
            return $this->calculerTauxRaid($raid);
        })->filter(function($taux) {
            return $taux !== null;
        });
        
        return $taux->isNotEmpty() ? $taux->avg() : 0;
    }

    private function calculerTauxRaid($raid)
    {
        $courses = $raid->sae_course;
        
        if ($courses->isEmpty()) {
            return null;
        }
        
        $totalParticipants = 0;
        $totalMax = 0;
        
        foreach ($courses as $course) {
            // Compte les participants réels via les équipes
            $nbParticipants = EQUIPE::where('sae_equipe.CRS_ID', $course->id)
                ->join('sae_appartenir', 'sae_equipe.EQU_ID', '=', 'sae_appartenir.EQU_ID')
                ->count();
            
            $totalParticipants += $nbParticipants;
            $totalMax += $course->nb_max_participants ?? 0;
        }
        
        return $totalMax > 0 ? ($totalParticipants / $totalMax) * 100 : 0;
    }

}

?>


