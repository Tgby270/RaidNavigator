<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class AgeCategory extends Model
    {
    protected $table = 'sae_cat_age';
    protected $primaryKey = 'AGE_ID';

    protected $fillable = ['AGE_MIN','AGE_MAX'];
    }

?>