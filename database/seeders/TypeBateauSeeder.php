<?php

namespace Database\Seeders;

use App\Models\TypeBateau;
use Illuminate\Database\Seeder;

/**
 * Seeder des types de bateaux.
 */
class TypeBateauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['nom' => 'porte-avions', 'taille' => 5],
            ['nom' => 'cuirasse', 'taille' => 4],
            ['nom' => 'destroyer', 'taille' => 3],
            ['nom' => 'sous-marin', 'taille' => 3],
            ['nom' => 'patrouilleur', 'taille' => 2],
        ];

        foreach ($types as $type) {
            TypeBateau::create($type);
        }
    }
}
