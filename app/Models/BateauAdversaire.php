<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BateauAdversaire extends Model
{
    protected $table = 'bateaux_adversaires';
    protected $fillable = ['partie_id', 'type_id', 'est_coule'];
}
