<?php

use App\Models\RAIDS;
use App\Models\COURSE;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RAIDController;
use App\Http\Controllers\ClubController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\TariferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AuthController;

use Inertia\Inertia;

Route::get('/', function () {
  $raids = RAIDS::where('raid_date_debut', '>=', now())
    ->orderBy('raid_date_debut')
    ->limit(3)
    ->get();

  $clubs = \App\Models\CLUB::select('CLU_ID', 'CLU_NOM')->orderBy('CLU_NOM')->get();

  return Inertia::render('Welcome', [
    'threeNextRaids' => $raids,
    'clubs' => $clubs,
    'OverlayLog' => false
  ]);
});

Route::get('/test', function () {
  if (!Auth::check()) {
    return redirect('/')->with('error', 'Vous devez vous connecter pour accéder à cette page.');
  } else {
    $controller = new RAIDController();
    $response = $controller->index();

    if ($response instanceof \Inertia\Response) {
      return $response;
    } else {
      return "Unexpected response type";
    }
  }
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
  // Création de raid
  Route::get('/CreateRaid', function (\Illuminate\Http\Request $request) {
    $users = User::all();
    $clubId = $request->query('club_id');
    $club = null;
    if ($clubId) {
      $club = \App\Models\CLUB::find($clubId);
    }
    return Inertia::render('Raid/CreateRaid', [
      'users' => $users,
      'club_id' => $clubId,
      'club' => $club
    ]);
  })->name('raids.create');

  // Alias pour /raid/{id}
  Route::get('/raid/{raid_id}', function ($raid_id) {
    $raid = RAIDS::findOrFail($raid_id);
    $courses = CourseController::getter($raid_id);
    return Inertia::render('All-RAIDS/RaidDetail', ['raid' => $raid, 'courses' => $courses]);
  });

  Route::get('/raid/manage/{raid_id}', function ($raid_id) {
    $raid = RAIDS::findOrFail($raid_id);

    // Redirect to read-only view if user is not the raid manager
    if (!Auth::check() || Auth::id() !== $raid->USE_ID) {
      return redirect("/raid/{$raid_id}");
    }

    $courses = COURSE::where('raid_id', $raid_id)->get();
    $isManager = true;
    return Inertia::render('All-RAIDS/RaidManage', [
      'raid' => $raid,
      'courses' => $courses,
      'isManager' => $isManager
    ]);
  });

  Route::post('/raid/create', [RAIDController::class, 'store'])->name('raids.store');

  // Création de course
  Route::get('/raids/{raid}/courses/create', [CourseController::class, 'create'])->name('courses.create');
  Route::post('/courses/{raid}/create', [CourseController::class, 'store'])->name('courses.store');

  // Ancienne route pour compatibilité (accepte raid_id en query)
  Route::get('CreateCourse', function (\Illuminate\Http\Request $request) {
    $users = User::all();
    $raidId = $request->query('raid_id');
    $raid = null;
    if ($raidId) {
      $raid = RAIDS::find($raidId);
    }
    return Inertia::render('Course/CreateCourse', [
      'users' => $users,
      'raid_id' => $raidId,
      'raid' => $raid
    ]);
  });

  // Création de club
  Route::get('/CreateClub', [ClubController::class, 'create'])->name('clubs.create');
  Route::post('/club/create', [ClubController::class, 'store'])->name('clubs.store');

  // Modification d'un club (édition)
  Route::get('/club/{id}/edit', [ClubController::class, 'edit'])->name('clubs.edit');
  Route::post('/club/{id}/update', [ClubController::class, 'update'])->name('clubs.update');

  // Gestion des tarifs
  Route::get('/raids/{raid}/courses/{crs}/tarifs', [TariferController::class, 'index']);
  Route::post('/raids/{raid}/courses/{crs}/tarifs', [TariferController::class, 'store']);
});

// Routes publiques
Route::get('CreateCourse', function (\Illuminate\Http\Request $request) {
  $users = User::all();
  $raidId = $request->query('raid_id');
  $raid = null;
  if ($raidId) {
    $raid = RAIDS::find($raidId);
  }
  return Inertia::render('Course/CreateCourse', [
    'users' => $users,
    'raid_id' => $raidId,
    'raid' => $raid
  ]);
});

// Edit course page (ModificationCourse)
Route::get('/course/{raid}/{crs}/edit', function ($raid, $crs) {
  $users = User::all();
  $course = COURSE::where('RAID_ID', $raid)->where('CRS_ID', $crs)->first();
  if (!$course) {
    abort(404);
  }

  // Fetch tariffs for this course (AGE_ID 1 = <18, AGE_ID 2 = >18)
  $tarifUnder = \App\Models\TARIFER::where('RAID_ID', $raid)->where('CRS_ID', $crs)->where('AGE_ID', 1)->first();
  $tarifOver = \App\Models\TARIFER::where('RAID_ID', $raid)->where('CRS_ID', $crs)->where('AGE_ID', 2)->first();

  $priceUnder18 = $tarifUnder ? $tarifUnder->PRIX : null;
  $priceOver18 = $tarifOver ? $tarifOver->PRIX : null;

  return Inertia::render('Course/ModificationCourse', [
    'users' => $users,
    'course' => $course,
    'raid_id' => $raid,
    'course_id' => $crs,
    'priceUnder18' => $priceUnder18,
    'priceOver18' => $priceOver18,
  ]);
});

Route::post('/raid/create', [RAIDController::class, 'store']);

Route::get('/raids/{raid}/courses/{crs}/tarifs', [TariferController::class, 'index']);

Route::post('/courses/create', [CourseController::class, 'store'])->name('courses.store');
Route::post('/courses/update', [CourseController::class, 'update'])->name('courses.update');
Route::post('/raids/{raid}/courses/{crs}/tarifs', [TariferController::class, 'store']);
Route::get('/raid-detail/{raid_id}', function ($raid_id) {
  return Inertia::render('All-RAIDS/RaidDetail', ['raid_id' => $raid_id]);
});

Route::get('/CreateClub', [ClubController::class, 'create']);

Route::post('/club/create', [ClubController::class, 'store']);

// Dans routes/web.php
Route::middleware('auth')->group(function () {

  Route::get('/UserAccount', [ProfileController::class, 'edit'])->name('user.account');

  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

  Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::get('/logs/{file}', function (string $file) {
  if ($file === 'laravel') {
    $content = Storage::disk('laravelLog')->get('laravel.log');
    return view('log', [
      'file' => 'laravel.log',
      'content' => $content,
      'route' => route('logs.delete', ['disk' => 'laravelLog', 'file' => 'laravel.log'])
    ]);
  } else {
    Log::debug("accessing log path : " . Storage::disk('log')->path("$file.log"));
    if (Storage::disk('log')->exists("$file.log")) {
      Log::debug("exists : OK");
      $content = Storage::disk('log')->get("$file.log");
      return view('log', [
        'file' => "$file.log",
        'content' => $content,
        'route' => null
      ]);
    } else {
      Log::debug("exists : OK");
      return "<h1>$file.log</h1><p style='color:red'>Not Found</p>";
    }
  }
});

Route::post('/logs/{disk}/{file}/delete', function (string $disk, string $file) {
  Storage::disk($disk)->delete($file);
  return Redirect::back();
})->name("logs.delete");

// ============================================================
// ROUTES PROTÉGÉES - NÉCESSITENT UNE AUTHENTIFICATION
// ============================================================
Route::middleware(['auth'])->group(function () {

  // ========== GESTION DES ÉQUIPES ==========
  Route::get('/equipe/create', function (\Illuminate\Http\Request $request) {
    $raid_id = $request->query('raid_id');
    $course_id = $request->query('course_id');

    // Get course info for team capacity and time
    $maxCapacity = null;
    $currentCourse = null;
    if ($raid_id && $course_id) {
      $currentCourse = App\Models\COURSE::where('RAID_ID', $raid_id)
        ->where('CRS_ID', $course_id)
        ->first();
      $maxCapacity = $currentCourse ? $currentCourse->CRS_MAX_PARTICIPANTS_EQUIPE : null;
    }

    // Get all users with availability status
    $users = App\Models\User::all()->map(function ($u) use ($raid_id, $course_id, $currentCourse) {
      $available = true;
      $unavailableReason = null;

      // Check if user is already in a team for this specific course
      $alreadyInThisCourse = App\Models\APPARTENIR::where('STU_ID', $u->USE_ID)
        ->where('RAID_ID', $raid_id)
        ->where('CRS_ID', $course_id)
        ->exists();
      if ($alreadyInThisCourse) {
        $available = false;
        $unavailableReason = 'Déjà dans une équipe pour cette course';
      }

      // Check if user has a conflicting course at the same time
      if ($available && $currentCourse && $currentCourse->CRS_DATE_HEURE_DEPART && $currentCourse->CRS_DATE_HEURE_FIN) {
        $conflictingCourse = App\Models\APPARTENIR::join('sae_course', function ($join) {
          $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
            ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID');
        })
          ->where('sae_appartenir.STU_ID', $u->USE_ID)
          ->where(function ($query) use ($currentCourse) {
            // Check for time overlap
            $query->whereBetween('sae_course.CRS_DATE_HEURE_DEPART', [
              $currentCourse->CRS_DATE_HEURE_DEPART,
              $currentCourse->CRS_DATE_HEURE_FIN
            ])
              ->orWhereBetween('sae_course.CRS_DATE_HEURE_FIN', [
                $currentCourse->CRS_DATE_HEURE_DEPART,
                $currentCourse->CRS_DATE_HEURE_FIN
              ])
              ->orWhere(function ($q) use ($currentCourse) {
              $q->where('sae_course.CRS_DATE_HEURE_DEPART', '<=', $currentCourse->CRS_DATE_HEURE_DEPART)
                ->where('sae_course.CRS_DATE_HEURE_FIN', '>=', $currentCourse->CRS_DATE_HEURE_FIN);
            });
          })
          ->exists();

        if ($conflictingCourse) {
          $available = false;
          $unavailableReason = 'Conflit d\'horaire avec une autre course';
        }
      }

      return [
        'id' => $u->USE_ID,
        'nom' => $u->USE_NOM,
        'prenom' => $u->USE_PRENOM,
        'email' => $u->USE_MAIL,
        'available' => $available,
        'unavailableReason' => $unavailableReason
      ];
    });

    return Inertia::render('Equipe/creationEquipe', [
      'raid_id' => $raid_id,
      'course_id' => $course_id,
      'users' => $users,
      'maxCapacity' => $maxCapacity
    ]);
  })->name('equipe.create');

  Route::get('/equipe/modify/{raid_id}/{course_id}/{equ_id?}', function ($raid_id, $course_id, $equ_id = null) {
    // Get course info for time checking
    $currentCourse = App\Models\COURSE::where('RAID_ID', $raid_id)
      ->where('CRS_ID', $course_id)
      ->first();

    // Get all users with availability status
    $users = App\Models\User::all()->map(function ($u) use ($raid_id, $course_id, $currentCourse) {
      $available = true;
      $unavailableReason = null;

      // Check if user is already in a team for this specific course
      $alreadyInThisCourse = App\Models\APPARTENIR::where('STU_ID', $u->USE_ID)
        ->where('RAID_ID', $raid_id)
        ->where('CRS_ID', $course_id)
        ->exists();
      if ($alreadyInThisCourse) {
        $available = false;
        $unavailableReason = 'Déjà dans une équipe pour cette course';
      }

      // Check if user has a conflicting course at the same time
      if ($available && $currentCourse && $currentCourse->CRS_DATE_HEURE_DEPART && $currentCourse->CRS_DATE_HEURE_FIN) {
        $conflictingCourse = App\Models\APPARTENIR::join('sae_course', function ($join) {
          $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
            ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID');
        })
          ->where('sae_appartenir.STU_ID', $u->USE_ID)
          ->where(function ($query) use ($currentCourse) {
            // Check for time overlap
            $query->whereBetween('sae_course.CRS_DATE_HEURE_DEPART', [
              $currentCourse->CRS_DATE_HEURE_DEPART,
              $currentCourse->CRS_DATE_HEURE_FIN
            ])
              ->orWhereBetween('sae_course.CRS_DATE_HEURE_FIN', [
                $currentCourse->CRS_DATE_HEURE_DEPART,
                $currentCourse->CRS_DATE_HEURE_FIN
              ])
              ->orWhere(function ($q) use ($currentCourse) {
              $q->where('sae_course.CRS_DATE_HEURE_DEPART', '<=', $currentCourse->CRS_DATE_HEURE_DEPART)
                ->where('sae_course.CRS_DATE_HEURE_FIN', '>=', $currentCourse->CRS_DATE_HEURE_FIN);
            });
          })
          ->exists();

        if ($conflictingCourse) {
          $available = false;
          $unavailableReason = 'Conflit d\'horaire avec une autre course';
        }
      }

      return [
        'id' => $u->USE_ID,
        'nom' => $u->USE_NOM,
        'prenom' => $u->USE_PRENOM,
        'email' => $u->USE_MAIL,
        'available' => $available,
        'unavailableReason' => $unavailableReason
      ];
    });

    // Try to find the team by provided equ_id, otherwise try to find the current user's team for the course
    $equipeModel = null;
    if ($equ_id) {
      $equipeModel = App\Models\EQUIPE::where('RAID_ID', $raid_id)
        ->where('CRS_ID', $course_id)
        ->where('EQU_ID', $equ_id)
        ->first();
    } else {
      $userId = Auth::id();
      if ($userId) {
        $app = App\Models\APPARTENIR::where('RAID_ID', $raid_id)
          ->where('CRS_ID', $course_id)
          ->where('STU_ID', $userId)
          ->first();
        if ($app) {
          $equipeModel = App\Models\EQUIPE::where('RAID_ID', $raid_id)
            ->where('CRS_ID', $course_id)
            ->where('EQU_ID', $app->EQU_ID)
            ->first();
          // Set equ_id for props if found
          $equ_id = $app->EQU_ID;
        }
      }
    }

    $equipe = null;
    $maxCapacity = null;

    if ($equipeModel) {
      // Get course info for team capacity
      $course = App\Models\COURSE::where('RAID_ID', $raid_id)
        ->where('CRS_ID', $course_id)
        ->first();
      $maxCapacity = $course ? $course->CRS_MAX_PARTICIPANTS_EQUIPE : null;

      // Get all members from sae_appartenir
      $membres = App\Models\User::join('sae_appartenir', 'sae_users.USE_ID', '=', 'sae_appartenir.STU_ID')
        ->where('sae_appartenir.RAID_ID', $raid_id)
        ->where('sae_appartenir.CRS_ID', $course_id)
        ->where('sae_appartenir.EQU_ID', $equipeModel->EQU_ID)
        ->select('sae_users.USE_ID as id', 'sae_users.USE_NOM as nom', 'sae_users.USE_PRENOM as prenom', 'sae_users.USE_MAIL as email', 'sae_appartenir.APP_STATUT as statut')
        ->get();

      // Get manager information
      $manager = App\Models\User::where('USE_ID', $equipeModel->USE_ID)
        ->select('USE_ID as id', 'USE_NOM as nom', 'USE_PRENOM as prenom')
        ->first();

      $equipe = [
        'EQU_ID' => $equipeModel->EQU_ID,
        'RAID_ID' => $equipeModel->RAID_ID,
        'CRS_ID' => $equipeModel->CRS_ID,
        'NOM_EQUIPE' => $equipeModel->EQU_NOM,
        'IMAGE_EQUIPE' => $equipeModel->EQU_IMAGE,
        'membres' => $membres->toArray(),
        'manager' => $manager ? $manager->toArray() : null,
        'max_capacity' => $maxCapacity,
        // also expose lowercase keys for frontend convenience
        'raid_id' => $raid_id,
        'course_id' => $course_id,
        'equ_id' => $equipeModel->EQU_ID,
      ];
    } else {
      // Expose ids even when no team found so the form knows context
      $equipe = [
        'raid_id' => $raid_id,
        'course_id' => $course_id,
        'equ_id' => $equ_id,
      ];
    }

    return Inertia::render('Equipe/modificationEquipe', [
      'equipe' => $equipe,
      'users' => $users,
    ]);
  })->name('equipe.modify');

  Route::post('/equipe/store', [App\Http\Controllers\EquipeController::class, 'store'])->name('equipe.store');
  Route::put('/equipe/update/{equ}', [App\Http\Controllers\EquipeController::class, 'update'])->name('equipe.update');
  Route::post('/equipe/update/{equ}', [App\Http\Controllers\EquipeController::class, 'update']);
  Route::post('/equipe/add/{id}', [App\Http\Controllers\EquipeController::class, 'add'])->name('equipe.add');
  Route::post('/equipe/deleteMember/{id}', [App\Http\Controllers\EquipeController::class, 'deleteMember'])->name('equipe.deleteMember');
  Route::delete('/equipe/deleteMember/{id}', [App\Http\Controllers\EquipeController::class, 'deleteMember']);
  Route::get('/equipe/deleteMember/{id}', [App\Http\Controllers\EquipeController::class, 'deleteMember']);
  Route::delete('/equipe/{raid}/{crs}/{equ}', [App\Http\Controllers\EquipeController::class, 'destroy'])->name('equipe.destroy');
  Route::post('/equipe/{raid}/{crs}/{equ}', [App\Http\Controllers\EquipeController::class, 'delete']);

  // Invitation routes (nécessitent d'être connecté pour accepter/refuser)
  Route::get('/invitation/accept', [App\Http\Controllers\EquipeController::class, 'acceptInvitation'])->name('invitation.accept');
  Route::get('/invitation/decline', [App\Http\Controllers\EquipeController::class, 'declineInvitation'])->name('invitation.decline');
});

// ============================================================
// ROUTES PUBLIQUES
// ============================================================
Route::get('/equipe', function () {
  return Inertia::render('Equipe/equipe');
});

Route::get('/course-detail/{course_id}/{raid_id}', [App\Http\Controllers\EquipeController::class, 'showCourseDetails'])->name('course.detail');

// JSON endpoint to fetch team details (manager + members)
Route::get('/equipe/{team}/details', [App\Http\Controllers\EquipeController::class, 'teamDetails'])->name('equipe.details');

Route::get('/course-detail/{course_id}/{raid_id}/files', [App\Http\Controllers\EquipeController::class, 'showGestion'])->name('course.gestion');

// Invitation routes
Route::get('/invitation/accept', [App\Http\Controllers\EquipeController::class, 'acceptInvitation'])->name('invitation.accept');
Route::get('/invitation/decline', [App\Http\Controllers\EquipeController::class, 'declineInvitation'])->name('invitation.decline');

// Quick test route to preview CourseDetails without parameters
Route::get('/course-detail-test', function () {
  return Inertia::render('Course/CourseDetails');
});
Route::get(('/login'), function () {
  $authController = new AuthController();
  return $authController->showLoginForm();
})->name('login');

Route::get(('/register'), function () {
  $authController = new AuthController();
  return $authController->showRegistrationForm();
})->name('register');



Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/logout', function (\Illuminate\Http\Request $request) {
  $authController = new AuthController();
  return $authController->logout($request);
})->name('logout');


Route::get('/mail', function () {
  return Inertia::render('TestMail');
});

Route::get('/invitation/accept', [MailController::class, 'accept'])->name('invitation.accept');
Route::get('/invitation/refuse', [MailController::class, 'refuse'])->name('invitation.refuse');

Route::get('/dashboard', function () {

  $clubId = \App\Models\ADHERER::getClubByUserId(Auth::id());
  $nb_raids = RAIDS::getRaidNumberByClub($clubId);
  Log::debug('dashboard debug', ['auth_id' => Auth::id(), 'club_id' => $clubId, 'nb_raids' => $nb_raids]);

  //get the RAIDS that the user is responsible for
  $raids_responsible = RAIDS::where('USE_ID', Auth::id())->get();

  //get the COURSES that the user is responsible for
  $courses_responsible = \App\Models\COURSE::join('sae_raids', 'sae_course.RAID_ID', '=', 'sae_raids.RAID_ID')
    ->where('sae_course.USE_ID', Auth::id())
    ->select('sae_course.*', 'sae_raids.raid_image', 'sae_raids.raid_lieu', 'sae_raids.RAID_ID')
    ->get();

  $club_responsible = \App\Models\CLUB::where('USE_ID', Auth::id())->exists();

  $site_responsible = \App\Models\QUALIFIER::where('USE_ID', Auth::id())->exists();
  // récupérer les clubs (affichage gestion de site/club)
  $clubs = \App\Models\CLUB::all();

  // Get user's club ID (either as manager or as member)
  // so if the user is a manager it uses the club he manages otherwise it uses the club he adheres to, just using different tables
  $userClubId = null;
  $managedClub = \App\Models\CLUB::where('USE_ID', Auth::id())->first();
  if ($managedClub) {
    $userClubId = $managedClub->CLU_ID;
  } else {
    $userClubId = app(\App\Models\ADHERER::class)->getClubByUserId(Auth::id());
  }
  $club_name = null;
  if ($managedClub) {
    $club_name = $managedClub->CLU_NOM;
  } elseif ($userClubId) {
    $clubModel = \App\Models\CLUB::find($userClubId);
    $club_name = $clubModel ? $clubModel->CLU_NOM : null;
  }

  return Inertia::render('Dashboard/Dashboard', [
    'nb_raids' => $nb_raids,
    'raids_responsible' => $raids_responsible,
    'courses_responsible' => $courses_responsible,
    'club_responsible' => $club_responsible,
    'site_responsible' => $site_responsible,
    'clubs' => $clubs,
    'numberRaid' => app(DashboardController::class)->countClubRaid($userClubId),
    'numberRaidYear' => app(DashboardController::class)->countRaidForAYearForAClub(date('Y'), $userClubId),
    'number_of_user' => app(DashboardController::class)->countUserForAClub($userClubId),
    'raidsClub' => app(DashboardController::class)->clubRaid($userClubId),
    'club_id' => $userClubId,
    'club_name' => $club_name,
  ]);
})->name('dashboard');

// Place the /equipe/store route here, outside of any function or if/else
Route::post('/equipe/store', [App\Http\Controllers\EquipeController::class, 'store'])->name('equipe.store');

Route::post('/clubs', [ClubController::class, 'store'])->name('clubs.store');
Route::middleware('auth')->group(function () {
  Route::get('my-raids', [RAIDController::class, 'myRaids'])->name('my.raids');
  Route::get('my-raids/{raid_id}', [RAIDController::class, 'myRaidDetail'])->name('my.raid.detail');
});

// Results import route
Route::post('/results/import', function (\Illuminate\Http\Request $request) {
  if (!$request->file('csv_file')) {
    return response()->json(['message' => 'Aucun fichier fourni'], 400);
  }

  $file = $request->file('csv_file');
  $raid_id = $request->input('raid_id');
  $course_id = $request->input('course_id');

  // Validate user is the course manager
  $course = \App\Models\COURSE::where('RAID_ID', $raid_id)->where('CRS_ID', $course_id)->first();
  if (!$course || !Auth::check() || Auth::id() !== $course->USE_ID) {
    return response()->json(['message' => 'Accès refusé'], 403);
  }

  try {
    $csvLines = file($file->getRealPath());

    // Detect delimiter (comma or semicolon)
    $delimiter = ',';
    if (!empty($csvLines[0])) {
      if (substr_count($csvLines[0], ';') > substr_count($csvLines[0], ',')) {
        $delimiter = ';';
      }
    }

    $csv = array_map(function ($line) use ($delimiter) {
      return str_getcsv($line, $delimiter);
    }, $csvLines);
    $headers = array_shift($csv);

    // Filter empty rows
    $csv = array_filter($csv, function ($row) {
      return !empty($row) && !empty($row[0]);
    });

    // Reference set: all teams registered for this course
    $courseTeams = \App\Models\EQUIPE::where('RAID_ID', $raid_id)
      ->where('CRS_ID', $course_id)
      ->get(['EQU_ID', 'EQU_NOM', 'RES_ID', 'RAID_ID', 'CRS_ID']);
    $courseTeamIds = $courseTeams->pluck('EQU_ID')->toArray();
    $courseTeamNames = $courseTeams->pluck('EQU_NOM', 'EQU_ID')->toArray();

    // Create a normalized map for matching (lowercase + trimmed)
    $teamNameMap = [];
    foreach ($courseTeams as $team) {
      $normalized = strtolower(trim($team->EQU_NOM));
      $teamNameMap[$normalized] = $team;
    }

    $matched = 0;
    $skipped = [];
    $matchedTeamIds = [];
    $debugRows = [];

    foreach ($csv as $idx => $row) {
      $debugRows[] = [
        'index' => $idx,
        'row_count' => count($row),
        'row_data' => $row,
      ];

      if (count($row) < 5)
        continue;

      $clt = trim($row[0]);
      $puce = trim($row[1]);
      $equipe_nom = trim($row[2]);
      $categorie = trim($row[3]);
      $temps_str = trim($row[4]);
      $points = isset($row[5]) ? intval($row[5]) : 0;

      // Skip empty team names
      if (empty($equipe_nom))
        continue;

      // Convert HH:MM:SS format to seconds if needed
      $temps = 0;
      if (strpos($temps_str, ':') !== false) {
        $parts = explode(':', $temps_str);
        if (count($parts) === 3) {
          $temps = intval($parts[0]) * 3600 + intval($parts[1]) * 60 + intval($parts[2]);
        }
      } else {
        $temps = intval($temps_str);
      }

      // Find team by name (case-insensitive, trim whitespace) using PHP map
      $normalized = strtolower(trim($equipe_nom));
      if (isset($teamNameMap[$normalized])) {
        $team = $teamNameMap[$normalized];

        // Create or update result
        $result = \App\Models\RESULTATS::updateOrCreate(
          ['RES_ID' => $team->RES_ID],
          [
            'RES_TEMPS' => $temps,
            'RES_POINTS' => $points
          ]
        );

        // Update team with result ID if not already set
        if (!$team->RES_ID) {
          $team->RES_ID = $result->RES_ID;
          $team->save();
        }

        $matched++;
        $matchedTeamIds[$team->EQU_ID] = true;
      } else {
        $skipped[] = $equipe_nom;
      }
    }

    // Determine missing teams (registered but not present in CSV)
    $missingTeams = array_values(array_map(function ($id) use ($courseTeamNames) {
      return $courseTeamNames[$id];
    }, array_diff($courseTeamIds, array_keys($matchedTeamIds))));

    // Only fail if there are unknown teams in CSV, allow partial imports
    if (!empty($skipped)) {
      return response()->json([
        'message' => 'Erreur : des équipes du CSV sont inconnues.',
        'equipes_importees' => $matched,
        'equipes_attendues' => count($courseTeamIds),
        'equipes_manquantes' => $missingTeams,
        'equipes_inconnues_dans_csv' => $skipped,
      ], 400);
    }

    if ($matched === 0) {
      return response()->json([
        'message' => 'Aucune équipe n\'a pu être importée.',
        'debug_info' => [
          'csv_rows_count' => count($csv),
          'registered_teams' => $courseTeamNames,
          'normalized_team_map' => array_keys($teamNameMap),
          'csv_team_names' => $skipped,
          'csv_rows_debug' => $debugRows,
        ]
      ], 400);
    }

    return response()->json([
      'message' => "Résultats importés avec succès ({$matched} ligne(s)).",
      'equipes_manquantes' => $missingTeams,
      'equipes_inconnues_dans_csv' => $skipped,
    ]);
  } catch (\Exception $e) {
    return response()->json(['message' => 'Erreur lors du parsing du CSV: ' . $e->getMessage()], 400);
  }
})->name('results.import');


Route::post('/equipe/{raid}/{crs}/{equ}/markPaid', [App\Http\Controllers\EquipeController::class, 'markPaid'])->name('equipe.markPaid');