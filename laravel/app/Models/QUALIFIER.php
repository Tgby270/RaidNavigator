<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QUALIFIER extends Model
{
    /** @use HasFactory<\Database\Factories\QUALIFIERFactory> */
    use HasFactory;

    protected $table = 'sae_qualifier';

    protected $fillable = [
        'ROLE_ID',
        'USE_ID',
    ];
}
