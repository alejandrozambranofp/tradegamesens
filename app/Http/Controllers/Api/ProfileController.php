<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Actualiza el perfil del usuario (Nombre y Avatar).
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // Actualizar el nombre
        $user->name = $request->name;

        // Procesar la imagen si el usuario subió una nueva
        if ($request->hasFile('avatar')) {
            // Eliminar el archivo físico anterior si existe
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Guardar la nueva imagen en 'avatars' dentro de storage/app/public
            $path = $request->file('avatar')->store('avatars', 'public');

            // Guardar la ruta que se usará en la base de datos
            $user->avatar = '/storage/' . $path;
        }

        $user->save();

        return new UserResource($user->load('roles'));
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function user(Request $request)
    {
        return new UserResource($request->user()->load('roles'));
    }
}
