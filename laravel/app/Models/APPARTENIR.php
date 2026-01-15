<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APPARTENIR extends Model
{
    /** @use HasFactory<\Database\Factories\APPARTENIRFactory> */
    use HasFactory;
    protected $table = 'sae_appartenir';
    // No single auto-incrementing primary key (composite keys used in migration)
    protected $primaryKey = null;
    public $incrementing = false;

    // Allow mass assignment for composite FK columns
    protected $fillable = [
        'RAID_ID',
        'CRS_ID',
        'EQU_ID',
        'STU_ID',
        'APP_STATUT',
    ];
}
