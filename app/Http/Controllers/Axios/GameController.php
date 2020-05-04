<?php

namespace App\Http\Controllers\Axios;

use App\Http\Controllers\Controller;
use App\Model\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function createGame(Request $request)
    {

        $ip = $request->ip();
        $owner = $request['players'];
        $owner[0]['location'] = $ip;

        $players = json_encode($owner);
        $gameModel = new Game();
        $key = $gameModel->generateGameKey(45);
        $options = json_encode($request['selectedOptions']);
        $rounds = $request['rounds'];
        $game = $gameModel::create([
            'key' => $key,
            'selected_options' => $options,
            'rounds' => $rounds,
            'players' => $players
        ]);


        return $game;
    }

    public function loadLobby($uniquecode)
    {

        $game = $this->loadGame($uniquecode);

        return view('game.template.gather-players.index',[
            'uniquecode' => $uniquecode,
            'game' => $game
        ]);
    }

    public function loadGame($uniquecode)
    {

        $game = Game::where('key',$uniquecode)->get();

        return $game->toJson();
    }

    //Todo build in namechecker.
    //TODO:: Build ipGrabber and insert that into the new user;
    public function PlayerJoinGame(Request $request,$uniquecode)
    {
        $game = Game::where('key',$uniquecode)->firstOrFail();

        $players = $game->players;

        $newPlayer = $request->all();

        $playersArray = json_decode($players,true);
        array_push($playersArray,$newPlayer);
        $playersJson = json_encode($playersArray);
        $newPlayerJson = json_encode($newPlayer);

        $game->players = $playersJson;
        $game->save();

        return $newPlayerJson;
    }

    public function playerCheck(Request $request,$uniquecode)
    {
        $game = Game::where('key',$uniquecode)->firstOrFail();
        $players = $game->players;

        $playersArray = json_decode($players);

        foreach($playersArray as $player){
            if($request->ip() === $player->location){
//            if('192.0.0.1' === $player->location){
                return json_encode($player,true);
            }
        }

        return "not found";
    }
}
