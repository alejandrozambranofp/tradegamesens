<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\User;
use App\Models\Game;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $games = Game::all();
        $categories = Category::all();

        if ($users->isEmpty() || $games->isEmpty()) {
            return;
        }

        $guideTemplates = [
            ['title' => 'Guía definitiva para principiantes', 'content' => 'En esta guía repasamos los conceptos básicos que todo jugador debe conocer...'],
            ['title' => 'Localización de secretos ocultos', 'content' => 'Existen multitud de secretos que los desarrolladores han escondido por el mapa...'],
            ['title' => 'Cómo optimizar tu equipamiento', 'content' => 'Para maximizar el daño es necesario combinar las piezas correctas de armadura...'],
            ['title' => 'Ruta de farmeo de experiencia rápida', 'content' => 'Si quieres subir de nivel en poco tiempo, sigue esta ruta circular...'],
            ['title' => 'Estrategia para el jefe final', 'content' => 'El jefe final tiene tres fases, en la primera debes mantener la distancia...'],
            ['title' => 'Coleccionables y dónde encontrarlos', 'content' => 'Hay un total de 50 coleccionables repartidos por los cinco actos del juego...'],
            ['title' => 'Mejores habilidades para el early game', 'content' => 'No gastes tus puntos de habilidad a lo loco, estas son las prioritarias...'],
            ['title' => 'Lore explicado: Todo lo que debes saber', 'content' => 'La historia del juego es profunda y llena de matices que se cuentan en notas...'],
            ['title' => 'Secretos del modo multijugador', 'content' => 'Si juegas con amigos, hay mecánicas que cambian por completo...'],
            ['title' => 'Logros difíciles de conseguir', 'content' => 'Para sacar el platino necesitarás mucha paciencia y seguir estos pasos...'],
            ['title' => 'Análisis de parches y cambios recientes', 'content' => 'La última actualización ha nerfeado algunas armas y bufeado otras...'],
            ['title' => 'Speedrun: Cómo pasarte el juego en 2 horas', 'content' => 'Esta ruta está diseñada para saltarse las cinemáticas y los combates lentos...'],

        ];

        foreach ($guideTemplates as $template) {
            $user = $users->random();
            $game = $games->random();

            $guide = Guide::create([
                'title' => $template['title'] . ' en ' . $game->title,
                'slug' => Str::slug($template['title'] . '-' . $game->title . '-' . rand(1, 999)),
                'content' => $template['content'] . ' <br><br> Contenido generado automáticamente para pruebas.',
                'user_id' => $user->id,
                'game_id' => $game->id,
                'status' => 'published',
            ]);

            if ($categories->count() > 0) {
                $randomCategories = $categories->random(min(3, $categories->count()))->pluck('id');
                $guide->categories()->attach($randomCategories);
            }
        }
    }
}
