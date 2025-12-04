<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VoteSystemController extends Controller
{
    public function index()
    {
        $systems = VoteSystem::all();
        return view('votes.index', compact('systems'));
    }

    public function showPlayers($id)
    {
        $system = VoteSystem::findOrFail($id);
        $players = $system->players; // Relation many-to-many

        return view('votes.players', compact('system', 'players'));
    }
}
