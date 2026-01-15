<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $USE_ID
 * @property string $USE_NOM
 * @property string $USE_PRENOM
 * @property string $USE_MAIL
 * @property string $USE_MDP
 * @property string|null $USE_DATE_NAISSANCE
 * @property string|null $USE_NUM_LICENCIE
 * @property string|null $USE_NUM_PPS
 * @property string|null $USE_ADRESSE
 * @property string|null $USE_TELEPHONE
 * @property string|null $USE_CODE_POSTAL
 * @property string|null $USE_VILLE
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sae_users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'USE_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'USE_NOM',
        'USE_PRENOM',
        'USE_MAIL',
        'USE_MDP',
        'USE_DATE_NAISSANCE',
        'USE_NUM_LICENCIE',
        'USE_NUM_PPS',
        'USE_ADRESSE',
        'USE_TELEPHONE',
        'USE_CODE_POSTAL',
        'USE_VILLE',
    ];

    /**
     * Get the password for the user (Laravel expects 'password' column).
     */
    public function getAuthPassword()
    {
        return $this->USE_MDP;
    }

    /**
     * Get the email for the user (Laravel expects 'email' column).
     */
    public function getEmailForPasswordReset()
    {
        return $this->USE_MAIL;
    }

    /**
     * Get the name of the unique identifier for the user.
     * This should return the primary key column name.
     */
    public function getAuthIdentifierName()
    {
        return 'USE_ID';
    }

    /**
     * Get the value of the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->USE_ID;
    }

    /**
     * Accessor for name attribute (to support Laravel conventions).
     */
    public function getNameAttribute()
    {
        return $this->USE_PRENOM . ' ' . $this->USE_NOM;
    }

    /**
     * Accessor for email attribute (to support Laravel conventions).
     */
    public function getEmailAttribute()
    {
        return $this->USE_MAIL;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'USE_MDP',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'USE_MDP' => 'hashed',
            'USE_DATE_NAISSANCE' => 'date',
        ];
    }
}
