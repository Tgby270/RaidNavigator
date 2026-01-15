<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CLUB extends Model
{
    use HasFactory;

    protected $table = 'sae_clubs';
    protected $primaryKey = 'CLU_ID';
    public $timestamps = true;
    
    protected $fillable = [
        'CLU_NOM',
        'CLU_ADRESSE',
        'CLU_CODE_POSTAL',
        'CLU_VILLE',
        'CLU_CONTACT',
        'USE_ID'
    ];

    
    public function responsable()
    {
        return $this->belongsTo(User::class, 'USE_ID', 'USE_ID');
    }
}
