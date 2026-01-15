<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdhererController extends Controller
{
    function getClubIdByUserId($user_id)
    {
        $club_id = \DB::table('sae_user')
            ->where('USE_ID', $user_id)
            ->value('CLU_ID');

        return $club_id;
    }
}
