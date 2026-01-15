<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\TARIFER;
use App\Models\AgeCategory;

class PriceController extends Controller
{
    public function store(Request $request, $raidId, $crsId)
    {
        $validated = $request->validate([
            'tiers' => ['required','array','min:1'],
            'tiers.*.age_min' => ['nullable','integer','min:0'],
            'tiers.*.age_max' => ['nullable','integer','min:0'],
            'tiers.*.prix'    => ['nullable','numeric','min:0'],
            'tiers.*.price'    => ['nullable','numeric','min:0'],
        ]);

        DB::transaction(function () use ($raidId, $crsId, $validated) {

            // on remplace les tarifs existants
            TARIFER::where('RAID_ID',$raidId)
                ->where('CRS_ID',$crsId)
                ->delete();

            // generate TAR_ID starting at 1 for this raid/crs
            $nextTarId = 1;

            foreach ($validated['tiers'] as $tier) {

                // chercher ou créer la tranche d’âge
                $age = AgeCategory::firstOrCreate([
                    'AGE_MIN' => $tier['age_min'] ?? null,
                    'AGE_MAX' => $tier['age_max'] ?? null,
                ]);

                // créer le tarif avec PRIX and TAR_ID
                TARIFER::create([
                    'RAID_ID' => $raidId,
                    'CRS_ID'  => $crsId,
                    'AGE_ID'  => $age->AGE_ID,
                    'TAR_ID'  => $nextTarId,
                    'PRIX'    => $tier['prix'] ?? $tier['price'],
                ]);

                $nextTarId++;
            }
        });

        return response()->json(['status' => 'ok'], 201);
    }

    public function index($raidId, $crsId)
    {
        return TARIFER::where('RAID_ID',$raidId)
            ->where('CRS_ID',$crsId)
            ->join('sae_cat_age','sae_cat_age.AGE_ID','=','sae_tarifer.AGE_ID')
            ->get([
                'sae_cat_age.AGE_ID',
                'AGE_MIN','AGE_MAX','PRIX'
            ]);
    }
}
?>