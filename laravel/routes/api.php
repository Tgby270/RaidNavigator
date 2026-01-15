<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\MailController;

/* -------------------------------------------------------------------------- */
/*                                API READ DATA                               */
/* -------------------------------------------------------------------------- */

/*OTHER ROUTES*/
Route::get('/', [APIController::class, 'connectionTest']);
Route::post('/contact', [MailController::class, 'send']);


/*USER DATA ROUTES*/
Route::prefix('/users/stats')->group(function () {
    Route::get('/total', [APIController::class, 'getNumberOfUsers']);
    Route::get('/total/unlicensed', [APIController::class, 'getNumberOfUnlicensedUsers']);
    Route::get('/total/licensed', [APIController::class, 'getNumberOfLicensedUsers']);
    Route::get('/city', [APIController::class, 'getNumberOfUsersPerCity']);

});

/*CLUB DATA ROUTES*/
Route::prefix('/clubs')->group(function () {
    Route::get('/all', [APIController::class, 'getAllClubs']);
    Route::get('/{id}', [APIController::class, 'getClubById']);
    Route::prefix('/stats')->group(function () {
        Route::get('/all', [APIController::class, 'getClubStats']);
        Route::get('/total', [APIController::class, 'getNbClubs']);
        Route::get('/by-city', [APIController::class, 'getNbClubsPerCity']);
    });
});

/*RAID DATA ROUTES*/
Route::prefix('/raids')->group(function () {
    Route::get('/all', [APIController::class, 'getAllRaids']);
    Route::get('/name/{name}', [APIController::class, 'getRaidByName']);
    Route::get('/name/{name}/races', [APIController::class, 'getRacesByRaidName']);
    Route::get('/{id}', [APIController::class, 'getRaidById']);
    Route::get('/{id}/races', [APIController::class, 'getRacesByRaidId']);
    Route::prefix('/stats')->group(function () {
        Route::get('/all', [APIController::class, 'getRaidStats']);
        Route::get('/total', [APIController::class, 'getNbRaids']);
    });
});

/*RACES DATA ROUTES*/
Route::prefix('/races')->group(function () {
    Route::get('/all', [APIController::class, 'getAllRaces']);
    Route::get('/type/{type}', [APIController::class, 'getRacesByType']);
    Route::get('/name/{name}', [APIController::class, 'getRaceByName']);
    Route::get('/{raidId}/{id}', [APIController::class, 'getRaceById']);
});

/*AGE CATEGORIES DATA ROUTES*/
Route::prefix('/age-categories')->group(function () {
    Route::get('/all', [APIController::class, 'getAgeCategories']);
    Route::get('/{id}', [APIController::class, 'getAgeCategoryById']);
    Route::get('/{id}/courses', [APIController::class, 'getCoursesByAgeCategoryId']);
});

/*TEAMS DATA ROUTES*/
Route::prefix('/teams')->group(function () {
    Route::get('/all', [APIController::class, 'getAllTeams']);
    Route::prefix('/payment-status')->group(function () {
        Route::get('/stats', [APIController::class, 'getNumberOfTeamsByPaymentStatus']);
        Route::get('/paid', [APIController::class, 'getAllPayedTeams']);
        Route::get('/unpaid', [APIController::class, 'getAllUnpayedTeams']);
    });
    Route::get('/{raidId}/{crsId}', [APIController::class, 'getTeamsByRaceId']);
    Route::get('/{raidId}/{crsId}/{equId}', [APIController::class, 'getTeamById']);
});

/* -------------------------------------------------------------------------- */
/*                               API WRITE DATA                               */
/* -------------------------------------------------------------------------- */
/**
 * Routes to add data to the database
 */

//Route to add a team,
Route::post('teams/add', [APIController::class, 'addTeam']);

//Route to add a raid,
Route::post('raids/add', [APIController::class, 'addRaid']);

//Route to add a race,
Route::post('races/add', [APIController::class, 'addRace']);

//Route to add a user,
Route::post('users/add', [APIController::class, 'addUser']);

//Route to add a club,
Route::post('clubs/add', [APIController::class, 'addClub']);