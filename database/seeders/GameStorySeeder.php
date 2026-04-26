<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use Illuminate\Support\Str;

class GameStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            ['title' => 'The Legend of Zelda', 'cover' => ''],
            ['title' => 'Assassin\'s Creed', 'cover' => ''],
            ['title' => 'Resident Evil', 'cover' => ''],
            ['title' => 'Soulsborne', 'cover' => ''],
            ['title' => 'Tomb Rider', 'cover' => ''],
            ['title' => 'God of War', 'cover' => ''],
            ['title' => 'The Witcher 3', 'cover' => ''],
            ['title' => 'Horizon Zero Down', 'cover' => ''],
            ['title' => 'The Last of Us Part 1', 'cover' => ''],
            ['title' => 'The Last of Us Part 2', 'cover' => ''],
            ['title' => 'Red Dead Redemption 2', 'cover' => ''],
            ['title' => 'Cyberpunk 2077', 'cover' => ''],
            ['title' => 'Mass Effect', 'cover' => ''],
            ['title' => 'BioShock', 'cover' => ''],
            ['title' => 'Uncharted 4', 'cover' => ''],
            ['title' => 'Persona 5', 'cover' => ''],
            ['title' => 'Final Fantasy VII Remake', 'cover' => ''],
            ['title' => 'Detroit: Become Human', 'cover' => ''],
            ['title' => 'Heavy Rain', 'cover' => ''],
            ['title' => 'Ghost of Tsushima', 'cover' => ''],
            ['title' => 'Control', 'cover' => ''],
            ['title' => 'Alan Wake', 'cover' => ''],
            ['title' => 'Hellblade: Senua\'s Sacrifice', 'cover' => ''],
            ['title' => 'Plague Tale: Innocence', 'cover' => ''],
            ['title' => 'Death Stranding', 'cover' => ''],
            ['title' => 'NieR: Automata', 'cover' => ''],
            ['title' => 'Star Wars Jedi: Survivor', 'cover' => ''],
            ['title' => 'Marvel\'s Spider-Man', 'cover' => ''],
            ['title' => 'Batman: Arkham City', 'cover' => ''],
            ['title' => 'Skyrim', 'cover' => ''],

            ['title' => 'Fallout: New Vegas', 'cover' => ''],
            ['title' => 'Dragon Age: Origins', 'cover' => ''],
            ['title' => 'Baldur\'s Gate 3', 'cover' => ''],
            ['title' => 'Dishonored', 'cover' => ''],
            ['title' => 'Prey', 'cover' => ''],
            ['title' => 'Dead Space Remake', 'cover' => ''],
            ['title' => 'Resident Evil 4 Remake', 'cover' => ''],
            ['title' => 'Silent Hill 2', 'cover' => ''],
            ['title' => 'Until Dawn', 'cover' => ''],
            ['title' => 'The Dark Pictures Anthology', 'cover' => ''],
            ['title' => 'Life is Strange', 'cover' => ''],
            ['title' => 'The Walking Dead Telltale', 'cover' => ''],
            ['title' => 'Wolfenstein: The New Order', 'cover' => ''],
            ['title' => 'Doom Eternal', 'cover' => ''],
            ['title' => 'Metro Exodus', 'cover' => ''],
            ['title' => 'Stalker: Shadow of Chernobyl', 'cover' => ''],
            ['title' => 'Deus Ex: Human Revolution', 'cover' => ''],
            ['title' => 'Half-Life 2', 'cover' => ''],
            ['title' => 'Portal 2', 'cover' => ''],
            ['title' => 'Metal Gear Solid V', 'cover' => ''],
        ];

        foreach ($games as $game) {
            $localCover = '/images/collections/' . strtoupper($game['title']) . '.webp';
            $cover = $game['cover'];

            // Si existe la imagen local con el nombre del juego en mayúsculas, la usamos
            if (file_exists(public_path($localCover))) {
                $cover = $localCover;
            }

            Game::updateOrCreate(
                ['slug' => Str::slug($game['title'])],
                ['title' => $game['title'], 'cover' => $cover]
            );
        }
    }
}
