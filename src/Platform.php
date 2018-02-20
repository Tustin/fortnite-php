<?php
namespace Fortnite;

use Fortnite\Exception\InvalidGameModeException;
use Fortnite\Model\FortniteStats;

class Platform {
    public $solo;
    public $duo;
    public $squad;

    public function __construct($platform) {
        foreach ($platform as $key => $mode) {
            switch ($key) {
                case "p2":
                $this->solo = new FortniteStats($mode);
                break;
                case "p9":
                $this->squad = new FortniteStats($mode);
                break;
                case "p10":
                $this->duo = new FortniteStats($mode);
                break;
                default:
                throw new InvalidGameModeException('Mode ' . $key . ' is invalid.'); // Will be thrown if a new game mode is added
            }
        }
    }
}