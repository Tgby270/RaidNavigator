<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ADHERER extends Model
{
    /** @use HasFactory<\Database\Factories\ADHERERFactory> */
    use HasFactory;

    protected $table = 'sae_adherer';
    
    // Composite primary key
    protected $primaryKey = ['CLU_ID', 'USE_ID'];
    public $incrementing = false;

    protected $fillable = [
        'CLU_ID',
        'USE_ID',
    ];

    /**
     * Get the club that this membership belongs to.
     */
    public function club()
    {
        return $this->belongsTo(CLUB::class, 'CLU_ID', 'CLU_ID');
    }

    /**
     * Get the user that this membership belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USE_ID', 'USE_ID');
    }

    public static function getClubByUserId($user_id)
    {
        $club_id = \DB::table('sae_adherer')
            ->where('USE_ID', $user_id)
            ->value('CLU_ID');

        return $club_id;
    }
}
