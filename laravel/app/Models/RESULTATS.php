<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RESULTATS extends Model
{
    /** @use HasFactory<\Database\Factories\RESULTATSFactory> */
    use HasFactory;

    protected $table = 'sae_resultats';
    protected $primaryKey = 'RES_ID';
    public $timestamps = true;
    protected $fillable = ['EQU_ID', 'RES_TEMPS', 'RES_POINTS'];
}

