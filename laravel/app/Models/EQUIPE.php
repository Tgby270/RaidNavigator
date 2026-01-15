<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EQUIPE extends Model
{
    /** @use HasFactory<\Database\Factories\EQUIPEFactory> */
    use HasFactory;

    protected $table = 'sae_equipe';
    protected $primaryKey = 'equ_id';
    public $timestamps = false;

    public function membres()
    {
        return $this->hasMany(APPARTENIR::class, 'EQU_ID', 'EQU_ID');
    }

    public function resultats()
    {
        // L'équipe a un RES_ID qui pointe vers la table resultats
        return $this->hasOne(RESULTATS::class, 'RES_ID', 'RES_ID');
    }

     public function users()
    {
        return $this->hasManyThrough(
            User::class,
            APPARTENIR::class,
            'EQU_ID',
            'USE_ID',
            'EQU_ID',
            'STU_ID'
        );
    }

    public function tarif()
    {
        return DB::table('sae_utilisateurs')
            ->join('sae_appartenir', 'sae_utilisateurs.use_id', '=', 'sae_appartenir.use_id')
            ->join('sae_equipe', function ($join) {
                $join->on('sae_appartenir.raid_id', '=', 'sae_equipe.raid_id')
                     ->on('sae_appartenir.crs_id', '=', 'sae_equipe.crs_id')
                     ->on('sae_appartenir.equ_id', '=', 'sae_equipe.equ_id');
            })
            ->join('sae_course', function ($join) {
                $join->on('sae_equipe.raid_id', '=', 'sae_course.raid_id')
                     ->on('sae_equipe.crs_id', '=', 'sae_course.crs_id');
            })
            ->join('sae_tarifer', function ($join) {
                $join->on('sae_course.raid_id', '=', 'sae_tarifer.raid_id')
                     ->on('sae_course.crs_id', '=', 'sae_tarifer.crs_id');
            })
            ->join('sae_cat_age', 'sae_tarifer.age_id', '=', 'sae_cat_age.age_id')
            ->where('sae_equipe.raid_id', 1)
            ->where('sae_equipe.crs_id', 1)
            ->where('sae_equipe.equ_id', 1)
            ->whereRaw('TIMESTAMPDIFF(YEAR, use_date_naissance, CURDATE()) BETWEEN age_min AND age_max')
            ->sum('sae_tarifer.PRIX');
    }
}
