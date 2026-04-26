<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Resources\UserResource;

class AvatarController extends Controller
{
    /**
     * Lista los avatares disponibles en la carpeta predefinida.
     */
    public function getPredefinedAvatars()
    {
        $directory = public_path('images/avatars/predefined');
        
        if (!File::exists($directory)) {
            return response()->json([]);
        }

        $files = File::files($directory);
        
        $avatars = collect($files)->map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'url' => asset('images/avatars/predefined/' . $file->getFilename())
            ];
        });

        return response()->json($avatars);
    }

    /**
     * Asigna un avatar predefinido al usuario.
     */
    public function selectAvatar(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        $user = Auth::user();
        $filePath = public_path('images/avatars/predefined/' . $request->filename);

        if (!File::exists($filePath)) {
            return response()->json(['message' => 'Avatar no encontrado'], 404);
        }

        // Limpiar avatares anteriores de Spatie
        $user->clearMediaCollection('avatars');

        // Añadir el nuevo desde el path local
        $user->addMedia($filePath)
             ->preservingOriginal()
             ->toMediaCollection('avatars');

        return new UserResource($user->load('roles'));
    }
}
