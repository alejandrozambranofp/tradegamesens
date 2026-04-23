<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Guide;
use App\Models\User;
use App\Models\Game;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos datos existentes para relacionar
        $user = User::first() ?? User::factory()->create();
        $game = Game::first() ?? Game::create([
            'title' => 'Elden Ring',
            'slug' => 'elden-ring',
            'cover' => 'https://example.com/elden-ring-cover.jpg'
        ]);
        $categories = Category::all();

        // Creamos un par de guías de prueba
        $guides = [
            [
                'title' => 'Cómo derrotar a Malenia',
                'content' => 'La clave para derrotar a Malenia es esquivar hacia la derecha...',
            ],
            [
                'title' => 'Localización de todas las armas sagradas',
                'content' => 'En esta guía veremos dónde encontrar cada arma del juego...',
            ],
        ];

        foreach ($guides as $data) {
            $guide = Guide::create([
                'title'   => $data['title'],
                'slug'    => Str::slug($data['title']),
                'content' => $data['content'],
                'user_id' => $user->id,
                'game_id' => $game->id,
                'status'  => 'published',
            ]);

            // Relacionamos con 2 categorías aleatorias (Tabla Pivot)
            if ($categories->count() > 0) {
                $randomCategories = $categories->random(min(2, $categories->count()))->pluck('id');
                $guide->categories()->attach($randomCategories);
            }
        }
    }
}
