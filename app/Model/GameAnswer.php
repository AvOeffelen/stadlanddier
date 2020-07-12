<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GameAnswer extends Model
{
    protected $table = 'game_answer';

    protected $fillable = ['game_id', 'game_round', 'player_cookie', 'answer', 'points', 'player_name','letter'];

    public function Game()
    {
        return $this->belongsTo(Game::class);
    }

    public function PossibleAnswers()
    {
        return $possibleAnswers = [
            'hoofdstad' => [
            ],
            'land' => [],
            'dier' => [],
            'sport' => [
                "Alpine", "Atletiek", "AutoCoureur",
                "BMX", "Baanwielrennen", "Badminton", "Ballonvaren", "Baseball", "Basketball", "Beachsoccer", "Beachtvolleybal", "Biatlon", "Biljart", "Blindenvoetbal", "Bobslee", "Bobsleeën", "Boccia", "Bodybuilding", "Boksen", "Bowelen", "Bowling", "Bridge",
                "CP","Cricket", "Curling",
                "Dammen", "Dansen", "Darts", "Driebanden", "Duatlon",
                "Endurance", "Eventing",
                "Floorball", "Freestyle skiën","Futsal",
                "Gewichtheffen", "Golf", "Gymnastiek",
                "Handbal", "Handboogschieten", "Hockey",
                "IJshockey", "IJsklimmen", "Inline skaten",
                "Jeu de boules","Jiu Jitsu", "Judo",
                "Kanoën", "Karate", "Karting", "Kendo", "Kitesurfen", "Klimmen", "Korfbal", "Kunstrolschaatsen", "Kuntstrijden",
                "Langebaanschaatsen", "Langlaufen",
                "Moderne Vijfkamp", "Motocross", "Mountainbike",
                "Paardrijden", "Padel", "Parachutespringen", "Petanque", "Poolbiljarten", "Powerliften",
                "Rally", "Ritmische gymnastiek", "Rodelen", "Roeien", "Rolhockey", "Rolstoeltennis", "Rugby",
                "Schaatsen", "Schaken", "Schermen", "Schermvliegen", "Schieten", "Schoonspringen", "Shorttrack", "Skaten", "Skeleton", "Skispringen", "Skiën", "Sleehockey", "Snooker", "Snowboarden", "Softball", "Squash", "Sumo", "Synchroonzwemmen",
                "Taekwondo","Tafeltennis","Tennis","Trampolinespringen","Triathlon","Turnen",
                "Veldrijden","Veldschieten","Voetbal","Volleybal",
                "Wakeboard","Waterpolo","Waterski","Wegrace","Wielrennen","Windsurfen","Worstelen","Wushu",
                "Zaalhockey","Zeilen","Zeilvliegen","Zweefvliegen","Zwemmen"
            ],
            'artiest' => [],
            'acteur' => [],
            'job' => [],
            'auto' => [],
        ];
    }


}

