<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'RPG', 'slug' => 'rpg'],
            ['name' => 'Mundo Abierto', 'slug' => 'mundo-abierto'],
            ['name' => 'Soulsborne', 'slug' => 'soulsborne'],
            ['name' => 'Acción', 'slug' => 'accion'],
            ['name' => 'Aventura', 'slug' => 'aventura'],
            ['name' => 'Shooter', 'slug' => 'shooter'],
            ['name' => 'Estrategia', 'slug' => 'estrategia'],
            ['name' => 'Supervivencia', 'slug' => 'supervivencia'],
            ['name' => 'Terror', 'slug' => 'terror'],
            ['name' => 'Plataformas', 'slug' => 'plataformas'],
            ['name' => 'Puzles', 'slug' => 'puzles'],
            ['name' => 'Lucha', 'slug' => 'lucha'],
            ['name' => 'Deportes', 'slug' => 'deportes'],
            ['name' => 'Carreras', 'slug' => 'carreras'],
            ['name' => 'Simulador', 'slug' => 'simulador'],
            ['name' => 'Sandbox', 'slug' => 'sandbox'],
            ['name' => 'Metroidvania', 'slug' => 'metroidvania'],
            ['name' => 'Roguelike', 'slug' => 'roguelike'],
            ['name' => 'Sigilo', 'slug' => 'sigilo'],
            ['name' => 'MMORPG', 'slug' => 'mmorpg'],
            ['name' => 'MOBA', 'slug' => 'moba'],
            ['name' => 'Battle Royale', 'slug' => 'battle-royale'],
            ['name' => 'Gacha', 'slug' => 'gacha'],
            ['name' => 'JRPG', 'slug' => 'jrpg'],
            ['name' => 'Novela Visual', 'slug' => 'novela-visual'],
            ['name' => 'Gestión', 'slug' => 'gestion'],
            ['name' => 'Construcción', 'slug' => 'construccion'],
            ['name' => 'Cartas', 'slug' => 'cartas'],
            ['name' => 'Ritmo', 'slug' => 'ritmo'],
            ['name' => 'Point & Click', 'slug' => 'point-and-click'],
            ['name' => 'Cooperativo', 'slug' => 'cooperativo'],
            ['name' => 'Competitivo', 'slug' => 'competitivo'],
            ['name' => 'Indie', 'slug' => 'indie'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
