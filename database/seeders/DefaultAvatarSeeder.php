<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\File;

class DefaultAvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $directory = public_path('images/avatars/predefined');

        if (!File::exists($directory)) {
            $this->command->warn("La carpeta de avatares predefinidos no existe: $directory");
            return;
        }

        $files = File::files($directory);
        
        if (count($files) === 0) {
            $this->command->warn("No hay archivos en la carpeta de avatares predefinidos.");
            return;
        }

        foreach ($users as $user) {
            // Pick a random avatar from the folder
            $randomFile = $files[array_rand($files)];
            $filePath = $randomFile->getRealPath();

            // Clear existing and add the new one
            $user->clearMediaCollection('avatars');
            $user->addMedia($filePath)
                 ->preservingOriginal()
                 ->toMediaCollection('avatars');
        }

        $this->command->info("Avatares asignados correctamente a " . $users->count() . " usuarios.");
    }
}
