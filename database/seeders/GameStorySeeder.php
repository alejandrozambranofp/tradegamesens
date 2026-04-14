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
            ['title' => 'The Legend of Zelda', 'cover' => '/images/home/coll-zelda.png'],
            ['title' => 'Assassin\'s Creed', 'cover' => '/images/home/coll-ac.png'],
            ['title' => 'Resident Evil', 'cover' => 'https://images.unsplash.com/photo-1589241062272-c0a000072dfa?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Soulsborne', 'cover' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop'],
            ['title' => 'Tomb Rider', 'cover' => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'God of War', 'cover' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2000&auto=format&fit=crop'], // generic gaming images below
            ['title' => 'The Witcher 3: Wild Hunt', 'cover' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Horizon Zero Dawn', 'cover' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'The Last of Us Part I', 'cover' => 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'The Last of Us Part II', 'cover' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Red Dead Redemption 2', 'cover' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Cyberpunk 2077', 'cover' => 'https://images.unsplash.com/photo-1580234811497-9df7fd2f357e?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Mass Effect Legendary Edition', 'cover' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'BioShock', 'cover' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e047ce?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Uncharted 4: A Thief\'s End', 'cover' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Persona 5 Royal', 'cover' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Final Fantasy VII Remake', 'cover' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Detroit: Become Human', 'cover' => 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Heavy Rain', 'cover' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Ghost of Tsushima', 'cover' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Control', 'cover' => 'https://images.unsplash.com/photo-1580234811497-9df7fd2f357e?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Alan Wake', 'cover' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Hellblade: Senua\'s Sacrifice', 'cover' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e047ce?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Plague Tale: Innocence', 'cover' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Death Stranding', 'cover' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'NieR: Automata', 'cover' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Star Wars Jedi: Survivor', 'cover' => 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Marvel\'s Spider-Man', 'cover' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Batman: Arkham City', 'cover' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Skyrim', 'cover' => 'https://images.unsplash.com/photo-1580234811497-9df7fd2f357e?q=80&w=2000&auto=format&fit=crop'],
            // To reach 50, adding 20 more
            ['title' => 'Fallout: New Vegas', 'cover' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Dragon Age: Origins', 'cover' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e047ce?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Baldur\'s Gate 3', 'cover' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Dishonored', 'cover' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Prey', 'cover' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Dead Space Remake', 'cover' => 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Resident Evil 4 Remake', 'cover' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Silent Hill 2', 'cover' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Until Dawn', 'cover' => 'https://images.unsplash.com/photo-1580234811497-9df7fd2f357e?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'The Dark Pictures Anthology', 'cover' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Life is Strange', 'cover' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e047ce?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'The Walking Dead Telltale', 'cover' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Wolfenstein: The New Order', 'cover' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Doom Eternal', 'cover' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Metro Exodus', 'cover' => 'https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Stalker: Shadow of Chernobyl', 'cover' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Deus Ex: Human Revolution', 'cover' => 'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Half-Life 2', 'cover' => 'https://images.unsplash.com/photo-1580234811497-9df7fd2f357e?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Portal 2', 'cover' => 'https://images.unsplash.com/photo-1551103782-8ab07afd45c1?q=80&w=2000&auto=format&fit=crop'],
            ['title' => 'Metal Gear Solid V', 'cover' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e047ce?q=80&w=2000&auto=format&fit=crop'],
        ];

        foreach ($games as $game) {
            Game::updateOrCreate(
                ['slug' => Str::slug($game['title'])],
                ['title' => $game['title'], 'cover' => $game['cover']]
            );
        }
    }
}
