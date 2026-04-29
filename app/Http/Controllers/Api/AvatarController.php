<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $sourcePath = public_path('images/avatars/predefined/' . $request->filename);

        if (!File::exists($sourcePath)) {
            return response()->json(['message' => 'Avatar no encontrado'], 404);
        }

        // Definir el destino en storage/app/public/avatars
        $extension = File::extension($sourcePath);
        $newFilename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
        $destinationPath = 'avatars/' . $newFilename;

        // Eliminar el avatar anterior si existe
        if ($user->avatar) {
            $oldPath = str_replace('/storage/', '', $user->avatar);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Copiar el archivo al storage público
        Storage::disk('public')->put($destinationPath, File::get($sourcePath));

        // Actualizar el campo avatar en el usuario
        $user->avatar = '/storage/' . $destinationPath;
        $user->save();

        return new UserResource($user->load('roles'));
    }
}
