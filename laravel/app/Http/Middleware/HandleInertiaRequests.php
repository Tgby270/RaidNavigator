<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\RAIDS;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $myRaidsCount = 0;
        if ($request->user()) {
            $userId = $request->user()->USE_ID;
            $myRaidsCount = RAIDS::join('sae_course', 'sae_course.RAID_ID', '=', 'sae_raids.RAID_ID')
                ->join('sae_appartenir', function ($join) {
                    $join->on('sae_appartenir.RAID_ID', '=', 'sae_course.RAID_ID')
                         ->on('sae_appartenir.CRS_ID', '=', 'sae_course.CRS_ID');
                })
                ->where('sae_appartenir.STU_ID', $userId)
                ->distinct('sae_raids.RAID_ID')
                ->count('sae_raids.RAID_ID');
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->USE_ID,
                    'name' => ($request->user()->USE_PRENOM ?? '') . ' ' . ($request->user()->USE_NOM ?? ''),
                    'email' => $request->user()->USE_MAIL,
                ] : null,
            ],
            'flash' => [
                'status' => $request->session()->get('status'),
                'error' => $request->session()->get('error'),
            ],
            'myRaidsCount' => $myRaidsCount,
        ];
    }
}
