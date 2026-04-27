<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();
        
        DB::table('categories')->insert(array (
            0 => array ('name' => 'RPG', 'slug' => 'rpg', 'color' => '#e6194b'),
            1 => array ('name' => 'Mundo Abierto', 'slug' => 'mundo-abierto', 'color' => '#3cb44b'),
            2 => array ('name' => 'Soulsborne', 'slug' => 'soulsborne', 'color' => '#ffe119'),
            3 => array ('name' => 'Acción', 'slug' => 'accion', 'color' => '#4363d8'),
            4 => array ('name' => 'Aventura', 'slug' => 'aventura', 'color' => '#f58231'),
            5 => array ('name' => 'Shooter', 'slug' => 'shooter', 'color' => '#911eb4'),
            6 => array ('name' => 'Estrategia', 'slug' => 'estrategia', 'color' => '#46f0f0'),
            7 => array ('name' => 'Supervivencia', 'slug' => 'supervivencia', 'color' => '#f032e6'),
            8 => array ('name' => 'Terror', 'slug' => 'terror', 'color' => '#bcf60c'),
            9 => array ('name' => 'Plataformas', 'slug' => 'plataformas', 'color' => '#fabebe'),
            10 => array ('name' => 'Puzles', 'slug' => 'puzles', 'color' => '#008080'),
            11 => array ('name' => 'Lucha', 'slug' => 'lucha', 'color' => '#e6beff'),
            12 => array ('name' => 'Deportes', 'slug' => 'deportes', 'color' => '#9a6324'),
            13 => array ('name' => 'Carreras', 'slug' => 'carreras', 'color' => '#fffac8'),
            14 => array ('name' => 'Simulador', 'slug' => 'simulador', 'color' => '#800000'),
            15 => array ('name' => 'Sandbox', 'slug' => 'sandbox', 'color' => '#aaffc3'),
            16 => array ('name' => 'Metroidvania', 'slug' => 'metroidvania', 'color' => '#808000'),
            17 => array ('name' => 'Roguelike', 'slug' => 'roguelike', 'color' => '#ffd8b1'),
            18 => array ('name' => 'Sigilo', 'slug' => 'sigilo', 'color' => '#000075'),
            19 => array ('name' => 'MMORPG', 'slug' => 'mmorpg', 'color' => '#808080'),
            20 => array ('name' => 'MOBA', 'slug' => 'moba', 'color' => '#ffffff'),
            21 => array ('name' => 'Battle Royale', 'slug' => 'battle-royale', 'color' => '#000000'),
            22 => array ('name' => 'Gacha', 'slug' => 'gacha', 'color' => '#a9a9a9'),
            23 => array ('name' => 'JRPG', 'slug' => 'jrpg', 'color' => '#f0a3ff'),
            24 => array ('name' => 'Novela Visual', 'slug' => 'novela-visual', 'color' => '#0075dc'),
            25 => array ('name' => 'Gestión', 'slug' => 'gestion', 'color' => '#993f00'),
            26 => array ('name' => 'Construcción', 'slug' => 'construccion', 'color' => '#4c005c'),
            27 => array ('name' => 'Cartas', 'slug' => 'cartas', 'color' => '#191919'),
            28 => array ('name' => 'Ritmo', 'slug' => 'ritmo', 'color' => '#005c31'),
            29 => array ('name' => 'Point & Click', 'slug' => 'point-and-click', 'color' => '#2bce48'),
            30 => array ('name' => 'Cooperativo', 'slug' => 'cooperativo', 'color' => '#ffcc99'),
            31 => array ('name' => 'Competitivo', 'slug' => 'competitivo', 'color' => '#808080'),
            32 => array ('name' => 'Indie', 'slug' => 'indie', 'color' => '#94ffb5'),
        ));
    }
}