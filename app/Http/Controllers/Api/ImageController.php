<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        // 1. Validamos que sea una imagen real y que no pase de 10MB
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // 2. Creamos un nombre único para que no se machaquen fotos con el mismo nombre
            $nombreArchivo = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // 3. Guardamos la foto en 'storage/app/public/guides'
            $path = $file->storeAs('guides', $nombreArchivo, 'public');
            
            // 4. Devolvemos la URL pública para que Vue pueda mostrar la foto
            return response()->json([
                'url' => asset(Storage::url($path))
            ]);
        }

        return response()->json(['error' => 'Error al subir'], 400);
    }
}