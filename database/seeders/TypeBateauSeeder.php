<?php

namespace Database\Seeders;

use App\Models\TypeBateau;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            ['nom' => 'sous-marin', 'taille' => 2],
            ['nom' => 'patrouilleur', 'taille' => 1],
        ];

        foreach ($types as $type) {
            TypeBateau::create($type);
        }
    }
}
