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

    Route::get('/{uniquecode}/player-check','GameController@playerCheck')->name('game.owner.check');

});
