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
            $sourcePath = $randomFile->getRealPath();
            $extension = File::extension($sourcePath);
            
            // Definir el destino en storage/app/public/avatars
            $newFilename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
            $destinationPath = 'avatars/' . $newFilename;

            // Eliminar el avatar anterior si existe
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Copiar el archivo al storage público
            \Illuminate\Support\Facades\Storage::disk('public')->put($destinationPath, File::get($sourcePath));

            // Actualizar el campo avatar en el usuario
            $user->avatar = '/storage/' . $destinationPath;
            $user->save();
        }

        $this->command->info("Avatares asignados correctamente a " . $users->count() . " usuarios.");
    }
}
