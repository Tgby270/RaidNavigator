<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TARIFER extends Model
{
    protected $table = 'sae_tarifer';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'RAID_ID', 'CRS_ID', 'AGE_ID', 'TAR_ID', 'PRIX'
    ];
}

