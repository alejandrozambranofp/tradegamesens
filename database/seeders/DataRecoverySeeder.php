<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guide;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DataRecoverySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $games = Game::all();

        if ($users->isEmpty() || $games->isEmpty()) {
            return;
        }

        // 1. Recover Guide Images
        $guideFiles = Storage::disk('public')->files('guides');
        $existingGuideImages = Guide::whereNotNull('image')->pluck('image')->toArray();
        
        foreach ($guideFiles as $file) {
            $path = 'storage/' . $file;
            
            // Only link to existing guides that don't have an image yet
            if (!Guide::where('image', $path)->exists()) {
                $guide = Guide::whereNull('image')->first();
                
                if ($guide) {
                    $guide->update(['image' => $path]);
                }
            }
        }

        // 2. Recover User Avatars
        $avatarFiles = Storage::disk('public')->files('avatars');
        foreach ($avatarFiles as $index => $file) {
            $path = 'storage/' . $file;
            $user = $users->skip($index)->first() ?: $users->random();
            $user->update(['avatar' => $path]);
        }
    }
}
