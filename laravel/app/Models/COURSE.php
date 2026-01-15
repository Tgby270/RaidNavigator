<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COURSE extends Model
{
    use HasFactory;

    protected $table = 'sae_course';
    protected $primaryKey = ['RAID_ID', 'CRS_ID'];
    public $incrementing = false;

    protected $fillable = [
        'RAID_ID',
        'CRS_ID',
        'USE_ID',
        'CRS_NOM',
        'CRS_TYPE',
        'CRS_DUREE',
        'CRS_REDUC_LICENCIE',
        'CRS_DATE_HEURE_DEPART',
        'CRS_DATE_HEURE_FIN',
        'CRS_MIN_PARTICIPANTS',
        'CRS_MAX_PARTICIPANTS',
        'CRS_NB_EQUIPE_MIN',
        'CRS_NB_EQUIPE_MAX',
        'CRS_MAX_PARTICIPANTS_EQUIPE',
        'CRS_PRIX_REPAS',
        'CRS_PRIX_MOINS_18',
        'CRS_PRIX_PLUS_18',
        'CRS_DIFFICULTE',
    ];

    public function teams()
    {
        return $this->hasMany(EQUIPE::class, 'RAID_ID', 'RAID_ID')
            ->where('sae_equipe.CRS_ID', $this->CRS_ID);

    }

    protected $casts = [
        'CRS_DATE_HEURE_DEPART' => 'datetime',
        'CRS_DATE_HEURE_FIN' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'USE_ID', 'USE_ID');
    }

    public function raid()
    {
        return $this->belongsTo(RAIDS::class, 'RAID_ID', 'RAID_ID');
    }
}
