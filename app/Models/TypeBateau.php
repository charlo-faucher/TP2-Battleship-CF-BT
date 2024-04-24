<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeBateau extends Model
{
    protected $table = 'types_bateaux';

    public function parties(): HasMany
    {
        return $this->HasMany(BateauOrdinateur::class);
    }
}
