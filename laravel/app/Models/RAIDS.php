<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAIDS extends Model
{
    /** @use HasFactory<\Database\Factories\RAIDSFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sae_raids';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'RAID_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'USE_ID',
        'CLU_ID',
        'raid_nom',
        'raid_date_debut',
        'raid_date_fin',
        'raid_contact',
        'raid_site_web',
        'raid_lieu',
        'raid_image',
        'date_fin_inscription',
        'date_debut_inscription',
        'nombre_de_courses',
    ];

    protected $casts = [
        'raid_date_debut' => 'datetime',
        'raid_date_fin' => 'datetime',
        'date_fin_inscription' => 'datetime',
        'date_debut_inscription' => 'datetime',
    ];

    public function getRaidMng()
    {
        return $this->belongsTo(User::class, 'USE_ID', 'USE_ID');
    }

    public function getClub()
    {
        return $this->belongsTo(CLUB::class, 'CLU_ID', 'CLU_ID');
    }

    public function getNbRace()
    {
        return $this->nombre_de_courses;
    }

    public function getRaidName()
    {
        return $this->raid_nom;
    }

    public function getRaidImage()
    {
        return $this->raid_image;
    }

    public function getRaidStartDate()
    {
        return $this->raid_date_debut;
    }

    public function getRaidEndDate()
    {
        return $this->raid_date_fin;
    }

    public function getRaidLocation()
    {
        return $this->raid_lieu;
    }

    public function getRaidContact()
    {
        return $this->raid_contact;
    }

    public function getRaidWebsite()
    {
        return $this->raid_site_web;
    }

    public function getRegistrationStartDate()
    {
        return $this->date_debut_inscription;
    }

    public function getRegistrationEndDate()
    {
        return $this->date_fin_inscription;
    }
    public function courses()
    {
        return $this->hasMany(COURSE::class, 'RAID_ID', 'RAID_ID');
    }
    public static function getRaidNumberByClub($club_id)
    {
        $raid_count = \DB::table('sae_raids')
            ->where('CLU_ID', $club_id)
            ->count();

        return $raid_count;
    }
}
