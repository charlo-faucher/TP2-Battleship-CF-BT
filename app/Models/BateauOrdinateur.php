<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BateauOrdinateur extends Pivot
{
    protected $table = 'bateaux_ordinateur';

    public $incrementing = true;
    protected $fillable = ['partie_id', 'type_id'];

    public function partie() : BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }

    public function type() : BelongsTo
    {
        return $this->belongsTo(TypeBateau::class);
    }

    public function coordonnees() : HasMany
    {
        return $this->hasMany(CoordonneeBateauOrdinateur::class, 'bateau_id', 'id');
    }
}
