<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        // Retornamos todos los juegos usando el Resource
        return \App\Http\Resources\GameResource::collection(\App\Models\Game::all());
    }
}
