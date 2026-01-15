<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ROLE extends Model
{
    /** @use HasFactory<\Database\Factories\ROLEFactory> */
    use HasFactory;

    protected $table = 'sae_role';
    protected $primaryKey = 'ROLE_ID';

    protected $fillable = [
        'ROLE_LIBELLE',
    ];

    public $timestamps = true;
}
