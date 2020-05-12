<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Homecontroller@index')->name('index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'game', 'namespace' => 'Axios','middleware'=>['web']], function () {

    Route::post('/create-game','GameController@createGame')->name('axios.create.game');
    Route::get('/{uniquecode}/lobby','GameController@loadLobby')->name('game.lobby');

    Route::get('/{uniquecode}/load','GameController@loadGame')->name('game.load');
    Route::post('/{uniquecode}/player-join-game','GameController@playerJoinGame')->name('game.load');
    Route::get('/{uniquecode}/start','GameController@startGame')->name('game.start');
    Route::put('/{uniquecode}/set-player-connected','GameController@setPlayerConnected')->name('game.set-player.connected');
    Route::put('/{uniquecode}/set-player-not-ready','GameController@setPlayerNotReady')->name('game.set-player.not.ready');

    Route::put('checkCookie','GameController@getCookie')->name('game.check.cookie');

    Route::put('/{uniquecode}/set-progress','Gamecontroller@setProgress')->name('game.set.progress');
    Route::put('/{uniquecode}/set-round-progress','Gamecontroller@setRoundProgress')->name('game.set.progress');
    Route::put('/{uniquecode}/update-end-round','Gamecontroller@updateEndRound')->name('game.set.not.in.progress');
    Route::get('/{uniquecode}/letter/{letter}/make-array','Gamecontroller@startNewRound')->name('game.make.answer.array');


});
