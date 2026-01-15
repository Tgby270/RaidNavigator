<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MailController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Mail::to($data['email'])->send(new ContactMail($data));

        return response()->json(['message' => 'Email sent successfully']);
    }

    public function accept(Request $request)
    {
        $group = $request->query('group');
        $email = $request->query('email');

        

        return Inertia::render('InvitationResponse', [
            'status' => 'accepted',
            'group' => $group,
            'email' => $email,
            'message' => "Félicitations ! Vous avez accepté l'invitation à rejoindre le groupe {$group}."
        ]);
    }

    public function refuse(Request $request)
    {
        $group = $request->query('group');
        $email = $request->query('email');

        // Here you can add logic to save the refusal in database
        // For example: update invitation status to declined

        return Inertia::render('InvitationResponse', [
            'status' => 'refused',
            'group' => $group,
            'email' => $email,
            'message' => "Vous avez refusé l'invitation à rejoindre le groupe {$group}."
        ]);
    }

    public function sendInvitationEmail($email, $teamName, $raidId, $courseId, $teamId)
    {
        // Get user info
        $user = \App\Models\User::where('USE_MAIL', $email)->first();
        
        // Get course and raid info
        $course = \App\Models\COURSE::where('CRS_ID', $courseId)
                                      ->where('RAID_ID', $raidId)
                                      ->first();
        
        $raid = \App\Models\RAIDS::where('RAID_ID', $raidId)->first();
        
        // Get team manager
        $team = \App\Models\EQUIPE::where('RAID_ID', $raidId)
                                   ->where('CRS_ID', $courseId)
                                   ->where('EQU_ID', $teamId)
                                   ->first();
        
        $manager = \App\Models\User::where('USE_ID', $team->USE_ID)->first();
        
        // Get current members count
        $currentMembers = \App\Models\APPARTENIR::where('RAID_ID', $raidId)
                                                  ->where('CRS_ID', $courseId)
                                                  ->where('EQU_ID', $teamId)
                                                  ->count();
        
        $data = [
            'email' => $email,
            'userName' => $user ? ($user->USE_PRENOM . ' ' . $user->USE_NOM) : 'Participant',
            'teamName' => $teamName,
            'teamId' => $teamId,
            'managerName' => $manager ? ($manager->USE_PRENOM . ' ' . $manager->USE_NOM) : 'Manager',
            'raidId' => $raidId,
            'raidName' => $raid ? $raid->raid_nom : 'Non spécifié',
            'courseId' => $courseId,
            'courseName' => $course ? $course->CRS_NOM : 'Non spécifié',
            'courseDate' => $course && $course->CRS_DATE_HEURE_DEPART ? (is_string($course->CRS_DATE_HEURE_DEPART) ? date('d/m/Y', strtotime($course->CRS_DATE_HEURE_DEPART)) : $course->CRS_DATE_HEURE_DEPART->format('d/m/Y')) : 'À déterminer',
            'courseStartTime' => $course && $course->CRS_DATE_HEURE_DEPART ? (is_string($course->CRS_DATE_HEURE_DEPART) ? date('H:i', strtotime($course->CRS_DATE_HEURE_DEPART)) : $course->CRS_DATE_HEURE_DEPART->format('H:i')) : 'À déterminer',
            'courseDistance' => $course && $course->CRS_DISTANCE ? $course->CRS_DISTANCE . ' km' : 'Non spécifié',
            'currentMembers' => $currentMembers,
            'maxCapacity' => $course ? $course->CRS_MAX_PARTICIPANTS_EQUIPE : 'N/A',
        ];

        Mail::to($email)->send(new InvitationMail($data));
    }

    public function sendRemovalEmail($email, $teamName, $raidName, $courseName, $managerName)
    {
        $user = \App\Models\User::where('USE_MAIL', $email)->first();
        
        $data = [
            'userName' => $user ? ($user->USE_PRENOM . ' ' . $user->USE_NOM) : 'Participant',
            'teamName' => $teamName,
            'raidName' => $raidName,
            'courseName' => $courseName,
            'managerName' => $managerName,
        ];

        Mail::to($email)->send(new \App\Mail\RemovalMail($data));
    }

    public function sendTeamDeletionEmail($email, $teamName, $raidName, $courseName, $managerName)
    {
        $user = \App\Models\User::where('USE_MAIL', $email)->first();
        
        $data = [
            'userName' => $user ? ($user->USE_PRENOM . ' ' . $user->USE_NOM) : 'Participant',
            'teamName' => $teamName,
            'raidName' => $raidName,
            'courseName' => $courseName,
            'managerName' => $managerName,
        ];

        Mail::to($email)->send(new \App\Mail\TeamDeletionMail($data));
    }
}