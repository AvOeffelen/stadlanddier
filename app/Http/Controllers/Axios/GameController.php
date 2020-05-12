<?php

namespace App\Http\Controllers\Axios;

use App\CookieTokenHelper;
use App\Http\Controllers\Controller;
use App\Model\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Cookie;

class GameController extends Controller
{
    public function createGame(Request $request)
    {
        $cookieTokenGenerator = new CookieTokenHelper();
        $token = $cookieTokenGenerator->generateToken(15);

        $owner = $request['players'];
        $owner[0]['location'] = $token;

        $players = json_encode($owner);
        $gameModel = new Game();
        $key = $gameModel->generateGameKey(45);
        $options = json_encode($request['selectedOptions']);
        $rounds = $request['rounds'];

        $letters = $this->getRandomLetterArray($rounds);

        $json = json_encode($letters);

        $game = $gameModel::create([
            'selected_letters' => $json,
            'key' => $key,
            'selected_options' => $options,
            'rounds' => $rounds,
            'players' => $players,
            'round_time'  => $request['timePerRound']
        ]);

        $cookie = cookie('player_token', $token, 240);
        return response($game)->cookie($cookie);
    }

    public function loadLobby(Request $request, $uniquecode)
    {
        $game = $this->loadGame($uniquecode);

        return view('game.template.gather-players.index', [
            'uniquecode' => $uniquecode,
            'game' => $game,
            'cookie' => $request->cookie('player_token')
        ]);
    }

    public function loadGame($uniquecode)
    {

        $game = Game::where('key', $uniquecode)->get();

        return $game;
    }

    //TODO:: build in namechecker. If same name just add (1) behind it.
    public function PlayerJoinGame(Request $request, $uniquecode)
    {
        $game = Game::where('key', $uniquecode)->firstOrFail();

        $players = $game->players;

        $cookieTokenGenerator = new CookieTokenHelper();
        $token = $cookieTokenGenerator->generateToken(15);

        $newPlayer = $request->all();
        $newPlayer['location'] = $token;

        $playersArray = json_decode($players, true);
        array_push($playersArray, $newPlayer);
        $playersJson = json_encode($playersArray);
        $newPlayerJson = json_encode($newPlayer);

        $game->players = $playersJson;
        $game->save();

        $cookie = cookie('player_token', $token, 240);
        return response($newPlayerJson)->cookie($cookie);
    }

    public function startGame(Request $request, $uniquecode)
    {
        $game = Game::where('key', $uniquecode)->firstOrFail();
        if($game->started != 1){
            $game->started = 1;
            $game->update();
        }

        return view('game.template.game.index', [
            'uniquecode' => $uniquecode,
        ]);
    }

    public function getCookie(Request $request)
    {
        return $request->cookie('player_token');
    }

    public function setPlayerReady(Request $request, $uniquecode)
    {
        $game = Game::where('key', $uniquecode)->firstOrFail();

        $players = $game->players;
        $playersJson = json_decode($players);

        $readyPlayer = $request->all();

        foreach ($playersJson as $index => $player) {
            if ($readyPlayer['location'] === $player->location) {
                $player->ready = 1;
                $readyPlayer['ready'] = 1;
            }
        }

        $game->players = $playersJson;
        $game->update();

        return $readyPlayer;
    }
    public function setPlayerConnected(Request $request, $uniquecode)
    {
        $game = Game::where('key', $uniquecode)->firstOrFail();

        $players = $game->players;
        $playersJson = json_decode($players);

        $readyPlayer = $request->all();

        foreach ($playersJson as $index => $player) {
            if ($readyPlayer['location'] === $player->location) {
                $player->ready = 1;
                $player->connected = 1;
                $readyPlayer['ready'] = 1;
                $readyPlayer['connected'] = 1;
            }
        }

        $game->players = $playersJson;
        $game->update();

        return $readyPlayer;
    }

    function getRandomLetterArray(int $length): array
    {
        $letters = [];

        $availableLetters = str_split('abcdefghijklmnopqrstuvwxyz');
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, count($availableLetters) - 1);

            $letters[] = $availableLetters[$index];
            // Remove letter from the available letters array.
            array_splice($availableLetters, $index, 1);
        }
        return $letters;
    }

    public function setProgress($uniquecode)
    {
        $game = $this->findGame($uniquecode);
        $game->in_progress = 1;
        $playersJson = json_decode($game->players);

        //Setting player ready state to 0 in order to start the round.
        foreach($playersJson as $index => $player){
            $player->ready = 0;
        }

        $game->update();

        return $game;
    }

    //TODO::code the end of the game.
    public function updateEndRound($uniquecode)
    {
        $game = $this->findGame($uniquecode);
        $game->round_in_progress = 0;

        if($game->current_round < $game->rounds){
            $game->current_round++;
        }

        $game->update();

        return $game;
    }

    public function setRoundProgress($uniquecode)
    {
        $game = $this->findGame($uniquecode);
        $game->round_in_progress = 1;

        $game->update();

        return $game;
    }

    public function startNewRound($uniquecode,$letter)
    {

        $game = $this->findGame($uniquecode);

        $answerArray= $this->buildAnswerArray($game->selected_options,$letter);

        return json_encode($answerArray);
    }

    public function buildAnswerArray($subjects,$letter): array
    {
        $answersArray = [];

        $subjectsArray = json_decode($subjects);

        foreach($subjectsArray as $index => $subject){
            $answers[$subject] = '';
        }

        $answers['letter'] = $letter;
        return $answers;
    }

    private function findGame($uniquecode){
        $game = Game::where('key', $uniquecode)->firstOrFail();
        return $game;
    }

    public function setPlayerNotReady(Request $request,$uniquecode)
    {
        $game = $this->findGame($uniquecode);

        $players = $game->players;
        $playersJson = json_decode($players);

        $currentPlayer = $request->all();

        foreach ($playersJson as $index => $player) {
            if ($currentPlayer['location'] === $player->location) {
                $player->ready = 0;
                $currentPlayer['ready'] = 0;
            }
        }

        $game->players = $playersJson;
        $game->update();

        return $currentPlayer;

    }

    //TODO:: write this.
    public function compareAnswers()
    {

    }

}
