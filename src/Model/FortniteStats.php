<?php
namespace Fortnite\Model;

use Fortnite\Exception\InvalidStatException;

class FortniteStats {
    public $wins = 0;
    public $top3 = 0;
    public $top5 = 0;
    public $top6 = 0;
    public $top10 = 0;
    public $top12 = 0;
    public $top25 = 0;
    public $kills = 0;
    public $matches_played = 0;
    public $minutes_played = 0;
    public $score = 0;

    public function __construct($stats) {
        foreach ($stats as $key => $value) {
            switch ($key) {
                case "placetop1":
                $this->wins = $value;
                break;
                case "placetop3":
                $this->top3 = $value;
                break;
                case "placetop5":
                $this->top5 = $value;
                break;
                case "placetop6":
                $this->top6 = $value;
                break;
                case "placetop10":
                $this->top10 = $value;
                break;
                case "placetop12":
                $this->top12 = $value;
                break;
                case "placetop25":
                $this->top25 = $value;
                break;
                case "matchesplayed":
                $this->matches_played = $value;
                break;
                case "kills":
                $this->kills = $value;
                break;
                case "score":
                $this->score = $value;
                break;
                case "minutesplayed":
                $this->minutes_played = $value;
                break;
                case "lastmodified": break;
                default:
                throw new InvalidStatException('Stat name '. $key . ' is not supported'); // I expect a PR if someone runs into this exception
            }
        }
    }
}
