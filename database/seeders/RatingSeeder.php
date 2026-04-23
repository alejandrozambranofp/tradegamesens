<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $guides = Guide::all();

        if ($users->isEmpty() || $guides->isEmpty()) {
            return;
        }

        $comments = [
            '¡Increíble guía! Me ha servido de mucho.',
            'Muy bien explicada, gracias por el aporte.',
            'Un poco confusa en la segunda parte, pero útil.',
            'La mejor guía que he leído sobre este juego.',
            'Faltan algunos detalles, pero en general está bien.',
            '¡Gracias! Por fin he podido pasarme este nivel.',
            'No me ha funcionado muy bien, pero se agradece el esfuerzo.',
            'Excelente trabajo de investigación.',
        ];

        foreach ($guides as $guide) {
            // Cada guía tendrá entre 2 y 5 valoraciones de usuarios aleatorios
            $numRatings = rand(2, 5);
            $randomUsers = $users->random(min($numRatings, $users->count()));

            foreach ($randomUsers as $user) {
                // Evitamos que el autor se valore a sí mismo (opcional, pero queda mejor)
                if ($user->id === $guide->user_id) continue;

                Rating::create([
                    'user_id' => $user->id,
                    'guide_id' => $guide->id,
                    'score' => rand(3, 5), // Notas positivas para que se vea bonito
                    'comment' => $comments[array_rand($comments)],
                ]);
            }
        }
    }
}
