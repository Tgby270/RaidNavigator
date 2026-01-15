<?php

namespace App\Http\Controllers;

use App\Models\EQUIPE;
use App\Models\APPARTENIR;
use App\Models\User;
use App\Models\COURSE;
use App\Models\RAIDS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

class EquipeController extends Controller
{
    // Add member via request body (AJAX-friendly)
    public function add(\Illuminate\Http\Request $request, $id){
        $validated = $request->validate([
            'RAID_ID' => 'required|integer',
            'CRS_ID' => 'required|integer',
            'EQU_ID' => 'required|integer',
        ]);

        $raid = $validated['RAID_ID'];
        $crs = $validated['CRS_ID'];
        $equ = $validated['EQU_ID'];

        if (!User::where('USE_ID', $id)->exists()) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if user is already in any team for this course
        if (APPARTENIR::where('STU_ID', $id)->where('RAID_ID', $raid)->where('CRS_ID', $crs)->exists()) {
            return response()->json(['error' => 'User already in a team for this course'], 409);
        }

        // Check for time conflicts with other courses
        $currentCourse = COURSE::where('CRS_ID', $crs)->where('RAID_ID', $raid)->first();
        if ($currentCourse && $currentCourse->CRS_DATE_HEURE_DEPART && $currentCourse->CRS_DATE_HEURE_FIN) {
            $conflictingCourse = APPARTENIR::join('sae_course', function($join) {
                $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
                     ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID');
            })
            ->where('sae_appartenir.STU_ID', $id)
            ->where(function($query) use ($currentCourse) {
                $query->whereBetween('sae_course.CRS_DATE_HEURE_DEPART', [
                    $currentCourse->CRS_DATE_HEURE_DEPART,
                    $currentCourse->CRS_DATE_HEURE_FIN
                ])
                ->orWhereBetween('sae_course.CRS_DATE_HEURE_FIN', [
                    $currentCourse->CRS_DATE_HEURE_DEPART,
                    $currentCourse->CRS_DATE_HEURE_FIN
                ])
                ->orWhere(function($q) use ($currentCourse) {
                    $q->where('sae_course.CRS_DATE_HEURE_DEPART', '<=', $currentCourse->CRS_DATE_HEURE_DEPART)
                      ->where('sae_course.CRS_DATE_HEURE_FIN', '>=', $currentCourse->CRS_DATE_HEURE_FIN);
                });
            })
            ->exists();
            
            if ($conflictingCourse) {
                return response()->json(['error' => 'User has a conflicting course at the same time'], 409);
            }
        }

        // Check team capacity
        $course = COURSE::where('CRS_ID', $crs)->where('RAID_ID', $raid)->first();
        if ($course) {
            $currentMemberCount = APPARTENIR::where('RAID_ID', $raid)
                                            ->where('CRS_ID', $crs)
                                            ->where('EQU_ID', $equ)
                                            ->count();
            
            if ($currentMemberCount >= $course->CRS_MAX_PARTICIPANTS_EQUIPE) {
                return response()->json(['error' => 'Team is full'], 400);
            }
        }

        try {
            APPARTENIR::create([
                'STU_ID' => $id,
                'EQU_ID' => $equ,
                'CRS_ID' => $crs,
                'RAID_ID' => $raid,
                'APP_STATUT' => 'accepte' // Direct add by manager = accepted
            ]);
            
            // Send invitation email to the added member
            $user = User::where('USE_ID', $id)->first();
            $team = EQUIPE::where('RAID_ID', $raid)
                          ->where('CRS_ID', $crs)
                          ->where('EQU_ID', $equ)
                          ->first();
            
            if ($user && $user->USE_MAIL && $team) {
                try {
                    (new \App\Http\Controllers\MailController())->sendInvitationEmail(
                        $user->USE_MAIL,
                        $team->EQU_NOM,
                        $raid,
                        $crs,
                        $equ
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send invitation email: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add member', 'details' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true]);
    }

    // Delete member via request body (AJAX-friendly)
    public function deleteMember(\Illuminate\Http\Request $request, $id){
        // Accept RAID/CRS/EQU from body (POST/DELETE) or query string (GET/DELETE)
        $raid = $request->input('RAID_ID');
        $crs = $request->input('CRS_ID');
        $equ = $request->input('EQU_ID');

        // If not provided in body, attempt query string
        $raid = $raid ?? $request->query('RAID_ID');
        $crs = $crs ?? $request->query('CRS_ID');
        $equ = $equ ?? $request->query('EQU_ID');

        if (!is_numeric($raid) || !is_numeric($crs) || !is_numeric($equ)) {
            return response()->json(['error' => 'Missing or invalid RAID_ID/CRS_ID/EQU_ID'], 422);
        }

        $raid = (int) $raid;
        $crs = (int) $crs;
        $equ = (int) $equ;

        try {
            // Get user info before deletion
            $user = User::where('USE_ID', $id)->first();
            $team = EQUIPE::where('RAID_ID', $raid)
                          ->where('CRS_ID', $crs)
                          ->where('EQU_ID', $equ)
                          ->first();
            $course = COURSE::where('CRS_ID', $crs)
                            ->where('RAID_ID', $raid)
                            ->first();
            $raidInfo = RAIDS::where('RAID_ID', $raid)->first();
            $manager = User::where('USE_ID', $team->USE_ID)->first();
            
            $deleted = APPARTENIR::where('STU_ID', $id)
                      ->where('EQU_ID', $equ)
                      ->where('CRS_ID', $crs)
                      ->where('RAID_ID', $raid)
                      ->delete();

            if ($deleted === 0) {
                return response()->json(['error' => 'Membership not found'], 404);
            }
            
            // Send removal notification email
            if ($user && $user->USE_MAIL && $team && $course && $raidInfo && $manager) {
                try {
                    (new \App\Http\Controllers\MailController())->sendRemovalEmail(
                        $user->USE_MAIL,
                        $team->EQU_NOM,
                        $raidInfo->raid_nom,
                        $course->CRS_NOM,
                        $manager->USE_PRENOM . ' ' . $manager->USE_NOM
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send removal email: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete membership', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return response()->json(['error' => 'Failed to delete member', 'details' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true]);
    }

    function store(Request $request){
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'raid_id' => 'required|integer',
            'course_id' => 'required|integer',
            'members' => 'nullable|array',
            'members.*' => 'integer|exists:sae_users,USE_ID'
        ]);

        $userId = Auth::id();
        
        // Check team capacity
        $course = COURSE::where('CRS_ID', $validated['course_id'])
                        ->where('RAID_ID', $validated['raid_id'])
                        ->first();
        
        $selectedMembers = $validated['members'] ?? [];
        
        if ($course && count($selectedMembers) > $course->CRS_MAX_PARTICIPANTS_EQUIPE) {
            return redirect()->back()
                           ->withErrors(['members' => "Vous ne pouvez pas ajouter plus de {$course->CRS_MAX_PARTICIPANTS_EQUIPE} membres."])
                           ->withInput();
        }
        
        // Get the next EQU_ID for this course
        $maxEquId = EQUIPE::where('RAID_ID', $validated['raid_id'])
                          ->where('CRS_ID', $validated['course_id'])
                          ->max('EQU_ID');
        $nextEquId = $maxEquId ? $maxEquId + 1 : 1;

        $equipe = new EQUIPE();
        $equipe->RAID_ID = $validated['raid_id'];
        $equipe->CRS_ID = $validated['course_id'];
        $equipe->EQU_ID = $nextEquId;
        $equipe->USE_ID = $userId; // Team manager
        $equipe->EQU_NOM = $validated['nom'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipe_images', 'public');
            $equipe->EQU_IMAGE = $path;
        }

        $equipe->save();

        // Add selected members to the team with appropriate status
        foreach ($selectedMembers as $memberId) {
            APPARTENIR::create([
                'STU_ID' => $memberId,
                'EQU_ID' => $nextEquId,
                'CRS_ID' => $validated['course_id'],
                'RAID_ID' => $validated['raid_id'],
                'APP_STATUT' => ($memberId == $userId) ? 'accepte' : 'en_attente'
            ]);
        }

        // Send email invitations to all members except the manager
        /*foreach ($selectedMembers as $memberId) {
            // Skip sending email if the member is the manager
            if ($memberId == $userId) {
                continue;
            }
            
            $user = User::where('USE_ID', $memberId)->first();
            if ($user && $user->USE_MAIL) {
                try {
                    (new \App\Http\Controllers\MailController())->sendInvitationEmail(
                        $user->USE_MAIL,
                        $validated['nom'],
                        $validated['raid_id'],
                        $validated['course_id'],
                        $nextEquId
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send invitation email: ' . $e->getMessage());
                }
            }
        }*/

        return redirect()->route('course.detail', [
            'course_id' => $validated['course_id'],
            'raid_id' => $validated['raid_id']
        ]);
    }

    function tarif (){
        $tarif = EQUIPE::tarif();
        return $tarif;
    }

    // Delete an entire team by composite key (AJAX / API)
    public function destroy($raid, $crs, $equ)
    {
        // Get team and member info before deletion
        $team = EQUIPE::where('RAID_ID', $raid)
                      ->where('CRS_ID', $crs)
                      ->where('EQU_ID', $equ)
                      ->first();
        
        $course = COURSE::where('CRS_ID', $crs)
                        ->where('RAID_ID', $raid)
                        ->first();
        
        $raidInfo = RAIDS::where('RAID_ID', $raid)->first();
        
        $manager = $team ? User::where('USE_ID', $team->USE_ID)->first() : null;
        
        // Get all members
        $members = APPARTENIR::where('RAID_ID', $raid)
                             ->where('CRS_ID', $crs)
                             ->where('EQU_ID', $equ)
                             ->get();
        
        // Send deletion emails to all members
        /*if ($team && $course && $raidInfo && $manager) {
            foreach ($members as $member) {
                $user = User::where('USE_ID', $member->STU_ID)->first();
                if ($user && $user->USE_MAIL) {
                    try {
                        (new \App\Http\Controllers\MailController())->sendTeamDeletionEmail(
                            $user->USE_MAIL,
                            $team->EQU_NOM,
                            $raidInfo->raid_nom,
                            $course->CRS_NOM,
                            $manager->USE_PRENOM . ' ' . $manager->USE_NOM
                        );
                    } catch (\Exception $e) {
                        Log::error('Failed to send team deletion email: ' . $e->getMessage());
                    }
                }
            }
        }*/
        
        // Delete memberships first (sae_appartenir) and then the team
        APPARTENIR::where('RAID_ID', $raid)
                  ->where('CRS_ID', $crs)
                  ->where('EQU_ID', $equ)
                  ->delete();

        EQUIPE::where('RAID_ID', $raid)
              ->where('CRS_ID', $crs)
              ->where('EQU_ID', $equ)
              ->delete();

        return response()->json(['success' => true]);
    }

    // Fallback for traditional form POST with _method=DELETE
    public function delete($raid, $crs, $equ)
    {
        // Get team and member info before deletion
        $team = EQUIPE::where('RAID_ID', $raid)
                      ->where('CRS_ID', $crs)
                      ->where('EQU_ID', $equ)
                      ->first();
        
        $course = COURSE::where('CRS_ID', $crs)
                        ->where('RAID_ID', $raid)
                        ->first();
        
        $raidInfo = RAIDS::where('RAID_ID', $raid)->first();
        
        $manager = $team ? User::where('USE_ID', $team->USE_ID)->first() : null;
        
        // Get all members
        $members = APPARTENIR::where('RAID_ID', $raid)
                             ->where('CRS_ID', $crs)
                             ->where('EQU_ID', $equ)
                             ->get();
        
        /*// Send deletion emails to all members
        if ($team && $course && $raidInfo && $manager) {
            foreach ($members as $member) {
                $user = User::where('USE_ID', $member->STU_ID)->first();
                if ($user && $user->USE_MAIL) {
                    try {
                        (new \App\Http\Controllers\MailController())->sendTeamDeletionEmail(
                            $user->USE_MAIL,
                            $team->EQU_NOM,
                            $raidInfo->raid_nom,
                            $course->CRS_NOM,
                            $manager->USE_PRENOM . ' ' . $manager->USE_NOM
                        );
                    } catch (\Exception $e) {
                        Log::error('Failed to send team deletion email: ' . $e->getMessage());
                    }
                }
            }
        }*/
        
        APPARTENIR::where('RAID_ID', $raid)
                  ->where('CRS_ID', $crs)
                  ->where('EQU_ID', $equ)
                  ->delete();

        EQUIPE::where('RAID_ID', $raid)
              ->where('CRS_ID', $crs)
              ->where('EQU_ID', $equ)
              ->delete();

        return response()->json(['success' => true]);
    }

    // Mark team as paid (AJAX)
    public function markPaid($raid, $crs, $equ, Request $request)
    {
        if (!is_numeric($raid) || !is_numeric($crs) || !is_numeric($equ)) {
            return response()->json(['error' => 'Identifiants invalides.'], 400);
        }

        $raid = (int)$raid;
        $crs = (int)$crs;
        $equ = (int)$equ;

        // Set team as paid
        EQUIPE::where('RAID_ID', $raid)
              ->where('CRS_ID', $crs)
              ->where('EQU_ID', $equ)
              ->update(['EQU_EST_PAYEE' => 1]);

        // Re-fetch team with counts so frontend can update accordingly
        $team = EQUIPE::where('RAID_ID', $raid)
                      ->where('CRS_ID', $crs)
                      ->where('EQU_ID', $equ)
                      ->withCount([
                          'membres' => function ($q) use ($crs, $raid) {
                              $q->where('RAID_ID', $raid)
                                ->where('CRS_ID', $crs);
                          },
                          'membres as incomplete_membres_count' => function ($q) use ($crs, $raid) {
                              $q->where('RAID_ID', $raid)
                                ->where('CRS_ID', $crs)
                                ->join('sae_users', 'sae_appartenir.STU_ID', '=', 'sae_users.USE_ID')
                                ->where(function($q2) {
                                    $q2->where('sae_appartenir.APP_STATUT', 'en_attente')
                                       ->orWhere(function($q3) {
                                           $q3->whereNull('sae_users.USE_NUM_PPS')->orWhere('sae_users.USE_NUM_PPS', '');
                                       });
                                })
                                ->where(function($q4) {
                                    $q4->whereNull('sae_users.USE_NUM_LICENCIE')->orWhere('sae_users.USE_NUM_LICENCIE', '');
                                });
                          }
                      ])->first(['EQU_ID','EQU_NOM','RAID_ID','CRS_ID','EQU_EST_PAYEE']);

        if ($team) {
            $team->dossier_complet = ($team->EQU_EST_PAYEE && $team->membres_count > 0 && $team->incomplete_membres_count == 0);
        }

        return response()->json(['success' => true, 'team' => $team]);
    }

    public function update($equ_id, Request $request)
    {
        $raid = $request->input('RAID_ID');
        $crs = $request->input('CRS_ID');

        if (!is_numeric($raid) || !is_numeric($crs) || !is_numeric($equ_id)) {
            return redirect()->back()->withErrors(['error' => 'Identifiants d\'équipe invalides.']);
        }

        $raid = (int) $raid;
        $crs = (int) $crs;
        $equ_id = (int) $equ_id;

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // Update using the where clause directly
        $updateData = ['EQU_NOM' => $validated['nom']];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipe_images', 'public');
            $updateData['EQU_IMAGE'] = $path;
        }

        // Check if team exists first
        $teamExists = EQUIPE::where('RAID_ID', $raid)
                            ->where('CRS_ID', $crs)
                            ->where('EQU_ID', $equ_id)
                            ->exists();

        if (!$teamExists) {
            return redirect()->back()->withErrors(['error' => 'Équipe non trouvée.']);
        }

        EQUIPE::where('RAID_ID', $raid)
              ->where('CRS_ID', $crs)
              ->where('EQU_ID', $equ_id)
              ->update($updateData);

        return redirect()->route('course.detail', [
            'course_id' => $crs,
            'raid_id' => $raid
        ])->with('success', 'Équipe mise à jour avec succès.');
    }

    function getTeamAndUserNumberWithTeamId($teamId) {
        return EQUIPE::withCount('membres')->find($teamId);
    }

    function getTeamsByRace($raceId, $raidId) {
        $userId = Auth::id();
        
        // Check if user is in any team as a member
        $userTeamIdAsMember = APPARTENIR::where('STU_ID', $userId)
                                        ->where('CRS_ID', $raceId)
                                        ->where('RAID_ID', $raidId)
                                        ->value('EQU_ID');
        
        // Check if user is a manager of any team
        $userTeamIdAsManager = EQUIPE::where('USE_ID', $userId)
                                     ->where('CRS_ID', $raceId)
                                     ->where('RAID_ID', $raidId)
                                     ->value('EQU_ID');
        
        $query = EQUIPE::where('CRS_ID', $raceId)
                       ->where('RAID_ID', $raidId);
        

        
        $teams = $query->withCount([
                         'membres' => function ($query) use ($raceId, $raidId) {
                             $query->where('RAID_ID', $raidId)
                                   ->where('CRS_ID', $raceId);
                         },
                         'membres as incomplete_membres_count' => function ($query) use ($raceId, $raidId) {
                             $query->where('RAID_ID', $raidId)
                                   ->where('CRS_ID', $raceId)
                                   ->join('sae_users', 'sae_appartenir.STU_ID', '=', 'sae_users.USE_ID')
                                   ->where(function($q) {
                                       // Count members who are either 'en_attente' OR who have neither PPS nor licence
                                       $q->where('sae_appartenir.APP_STATUT', 'en_attente')
                                         ->orWhere(function($q2) {
                                             $q2->where(function($q3) {
                                                 $q3->whereNull('sae_users.USE_NUM_PPS')->orWhere('sae_users.USE_NUM_PPS', '');
                                             })
                                             ->where(function($q4) {
                                                 $q4->whereNull('sae_users.USE_NUM_LICENCIE')->orWhere('sae_users.USE_NUM_LICENCIE', '');
                                             });
                                         });
                                   });
                         }
                     ])->get(['EQU_ID', 'EQU_NOM', 'RAID_ID', 'CRS_ID', 'EQU_EST_PAYEE']);
        
        // Dossier is complete if the team is paid and has members and no incomplete members
        $teams->transform(function($team) {
            $team->dossier_complet = ($team->EQU_EST_PAYEE && $team->membres_count > 0 && $team->incomplete_membres_count == 0);
            return $team;
        });
        return $teams;
    }

    function showCourseDetails($course_id, $raid_id, $raceId = null) {
        $userId = Auth::id();
        $teams = $this->getTeamsByRace($course_id, $raid_id);
        
        // Get course data
        $course = COURSE::where('CRS_ID', $course_id)
                        ->where('RAID_ID', $raid_id)
                        ->first();
        
        // Check if user is in a team (either as manager or member)
        $userTeamMembership = APPARTENIR::where('STU_ID', $userId)
                                        ->where('CRS_ID', $course_id)
                                        ->where('RAID_ID', $raid_id)
                                        ->first();
        
        // Also check if user is a manager of a team
        $userTeamAsManager = EQUIPE::where('USE_ID', $userId)
                                   ->where('CRS_ID', $course_id)
                                   ->where('RAID_ID', $raid_id)
                                   ->first();
        
        $userTeam = null;
        $isInTeam = false;
        $isManager = false;
        $userTeamMembers = [];
        
        if ($userTeamMembership) {
            // User is a member - get the team details
            $userTeam = EQUIPE::where('CRS_ID', $course_id)
                              ->where('RAID_ID', $raid_id)
                              ->where('EQU_ID', $userTeamMembership->EQU_ID)
                              ->first();
            
            if ($userTeam) {
                $isInTeam = true;
                $isManager = ($userTeam->USE_ID == $userId);
                
                // Get all members of user's team
                $userTeamMembers = User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
                                       ->where('sae_appartenir.RAID_ID', $raid_id)
                                       ->where('sae_appartenir.CRS_ID', $course_id)
                                       ->where('sae_appartenir.EQU_ID', $userTeam->EQU_ID)
                                       ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_appartenir.APP_STATUT as statut')
                                       ->get();
            }
        } elseif ($userTeamAsManager) {
            // User is a manager but not in sae_appartenir
            $userTeam = $userTeamAsManager;
            $isInTeam = true;
            $isManager = true;
            
            // Get all members of the team
            $userTeamMembers = User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
                                   ->where('sae_appartenir.RAID_ID', $raid_id)
                                   ->where('sae_appartenir.CRS_ID', $course_id)
                                   ->where('sae_appartenir.EQU_ID', $userTeam->EQU_ID)
                                   ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_appartenir.APP_STATUT as statut')
                                   ->get();
        }
        
        // Get manager information if user has a team
        $userTeamManager = null;
        if ($userTeam) {
            $userTeamManager = User::where('USE_ID', $userTeam->USE_ID)
                                   ->select('USE_ID as id', 'USE_NOM as nom', 'USE_PRENOM as prenom')
                                   ->first();
        }

        // Get results for this course
        $results = EQUIPE::where('sae_equipe.RAID_ID', $raid_id)
                         ->where('sae_equipe.CRS_ID', $course_id)
                         ->whereNotNull('sae_equipe.RES_ID')
                         ->leftJoin('sae_resultats', 'sae_equipe.RES_ID', '=', 'sae_resultats.RES_ID')
                         ->select(
                             'sae_equipe.EQU_NOM',
                             'sae_equipe.EQU_ID',
                             'sae_resultats.RES_TEMPS',
                             'sae_resultats.RES_POINTS'
                         )
                         ->orderByDesc('sae_resultats.RES_POINTS')
                         ->get()
                         ->toArray();
        
        // Also include raid info so front-end can check registration dates
        $raid = RAIDS::find($raid_id);

        return Inertia::render('Course/CourseDetails', [
            'course_id' => $course_id,
            'raid_id' => $raid_id,
            'course' => $course,
            'raid' => $raid,
            'teams' => $teams,
            'userTeam' => $userTeam,
            'userTeamMembers' => $userTeamMembers,
            'userTeamManager' => $userTeamManager,
            'isInTeam' => $isInTeam,
            'isManager' => $isManager,
            'results' => $results
        ]);
    }

    // Render Gestion page with the same dataset as CourseDetails
    public function showGestion($course_id, $raid_id, $raceId = null) {
        $userId = Auth::id();
        $teams = $this->getTeamsByRace($course_id, $raid_id);

        // Get course data
        $course = COURSE::where('CRS_ID', $course_id)
                        ->where('RAID_ID', $raid_id)
                        ->first();

        // Check if user is in a team (either as manager or member)
        $userTeamMembership = APPARTENIR::where('STU_ID', $userId)
                                        ->where('CRS_ID', $course_id)
                                        ->where('RAID_ID', $raid_id)
                                        ->first();

        // Also check if user is a manager of a team
        $userTeamAsManager = EQUIPE::where('USE_ID', $userId)
                                   ->where('CRS_ID', $course_id)
                                   ->where('RAID_ID', $raid_id)
                                   ->first();

        $userTeam = null;
        $isInTeam = false;
        $isManager = false;
        $userTeamMembers = [];

        if ($userTeamMembership) {
            // User is a member - get the team details
            $userTeam = EQUIPE::where('CRS_ID', $course_id)
                              ->where('RAID_ID', $raid_id)
                              ->where('EQU_ID', $userTeamMembership->EQU_ID)
                              ->first();

            if ($userTeam) {
                $isInTeam = true;
                $isManager = ($userTeam->USE_ID == $userId);

                // Get all members of user's team
                $userTeamMembers = User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
                                       ->where('sae_appartenir.RAID_ID', $raid_id)
                                       ->where('sae_appartenir.CRS_ID', $course_id)
                                       ->where('sae_appartenir.EQU_ID', $userTeam->EQU_ID)
                                       ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_users.USE_NUM_PPS as pps', 'sae_users.USE_NUM_LICENCIE as licence', 'sae_appartenir.APP_STATUT as statut')
                                       ->get();
            }
        } elseif ($userTeamAsManager) {
            // User is a manager but not in sae_appartenir
            $userTeam = $userTeamAsManager;
            $isInTeam = true;
            $isManager = true;

            // Get all members of the team
            $userTeamMembers = User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
                                   ->where('sae_appartenir.RAID_ID', $raid_id)
                                   ->where('sae_appartenir.CRS_ID', $course_id)
                                   ->where('sae_appartenir.EQU_ID', $userTeam->EQU_ID)
                                   ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_users.USE_NUM_PPS as pps', 'sae_users.USE_NUM_LICENCIE as licence', 'sae_appartenir.APP_STATUT as statut')
                                   ->get();
        }

        // Get manager information if user has a team
        $userTeamManager = null;
        if ($userTeam) {
            $userTeamManager = User::where('USE_ID', $userTeam->USE_ID)
                                   ->select('USE_ID as id', 'USE_NOM as nom', 'USE_PRENOM as prenom')
                                   ->first();
        }

        // Also include raid info so front-end can check registration dates
        $raid = RAIDS::find($raid_id);

        // Get results for this course
        $results = EQUIPE::where('sae_equipe.RAID_ID', $raid_id)
                         ->where('sae_equipe.CRS_ID', $course_id)
                         ->whereNotNull('sae_equipe.RES_ID')
                         ->leftJoin('sae_resultats', 'sae_equipe.RES_ID', '=', 'sae_resultats.RES_ID')
                         ->select(
                             'sae_equipe.EQU_NOM',
                             'sae_equipe.EQU_ID',
                             'sae_resultats.RES_TEMPS',
                             'sae_resultats.RES_POINTS'
                         )
                         ->orderByDesc('sae_resultats.RES_POINTS')
                         ->get()
                         ->toArray();

        return Inertia::render('Course/Gestion', [
            'course_id' => $course_id,
            'raid_id' => $raid_id,
            'course' => $course,
            'raid' => $raid,
            'teams' => $teams,
            'userTeam' => $userTeam,
            'userTeamMembers' => $userTeamMembers,
            'userTeamManager' => $userTeamManager,
            'isInTeam' => $isInTeam,
            'isManager' => $isManager,
            'results' => $results
        ]);
    }

    // Return JSON details for a team (manager + members)
    public function teamDetails($teamId)
    {
        $team = EQUIPE::find($teamId);
        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $manager = User::where('USE_ID', $team->USE_ID)
                       ->select('USE_ID as id', 'USE_NOM as nom', 'USE_PRENOM as prenom')
                       ->first();

        $members = User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
                       ->where('sae_appartenir.EQU_ID', $team->EQU_ID)
                       ->where('sae_appartenir.CRS_ID', $team->CRS_ID)
                       ->where('sae_appartenir.RAID_ID', $team->RAID_ID)
                       ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_users.USE_NUM_PPS as pps', 'sae_users.USE_NUM_LICENCIE as licence', 'sae_appartenir.APP_STATUT as statut')
                       ->get();

        return response()->json([
            'team' => $team,
            'manager' => $manager,
            'members' => $members,
        ]);
    }


    // Accept invitation
    public function acceptInvitation(Request $request)
    {
        $teamId = $request->query('team');
        $raidId = $request->query('raid');
        $courseId = $request->query('course');
        $email = $request->query('email');

        // Find user by email
        $user = User::where('USE_MAIL', $email)->first();
        if (!$user) {
            return Inertia::render('Invitation/Response', [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ]);
        }

        // Find the invitation
        $invitation = APPARTENIR::where('STU_ID', $user->USE_ID)
                                ->where('EQU_ID', $teamId)
                                ->where('RAID_ID', $raidId)
                                ->where('CRS_ID', $courseId)
                                ->first();

        if (!$invitation) {
            return Inertia::render('Invitation/Response', [
                'success' => false,
                'message' => 'Invitation non trouvée.'
            ]);
        }

        // Update status to accepted
        APPARTENIR::where('STU_ID', $user->USE_ID)
                  ->where('EQU_ID', $teamId)
                  ->where('RAID_ID', $raidId)
                  ->where('CRS_ID', $courseId)
                  ->update(['APP_STATUT' => 'accepte']);

        return Inertia::render('Invitation/Response', [
            'success' => true,
            'message' => 'Vous avez accepté l\'invitation ! Vous faites maintenant partie de l\'équipe.',
            'teamId' => $teamId,
            'raidId' => $raidId,
            'courseId' => $courseId
        ]);
    }

    // Decline invitation
    public function declineInvitation(Request $request)
    {
        $teamId = $request->query('team');
        $raidId = $request->query('raid');
        $courseId = $request->query('course');
        $email = $request->query('email');

        // Find user by email
        $user = User::where('USE_MAIL', $email)->first();
        if (!$user) {
            return Inertia::render('Invitation/Response', [
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ]);
        }

        // Find and delete the invitation
        $deleted = APPARTENIR::where('STU_ID', $user->USE_ID)
                             ->where('EQU_ID', $teamId)
                             ->where('RAID_ID', $raidId)
                             ->where('CRS_ID', $courseId)
                             ->delete();

        if (!$deleted) {
            return Inertia::render('Invitation/Response', [
                'success' => false,
                'message' => 'Invitation non trouvée.'
            ]);
        }

        return Inertia::render('Invitation/Response', [
            'success' => true,
            'message' => 'Vous avez refusé l\'invitation.',
            'declined' => true
        ]);
    }
}