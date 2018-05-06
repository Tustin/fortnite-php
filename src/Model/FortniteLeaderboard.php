<?php
namespace Fortnite\Model;

use Fortnite\Exception\InvalidStatException;

class FortniteLeaderboard {
   public $rank = 0;
   public $accountid = null;
   public $score = 0;
   public $displayname = null;

    /**
     * Constructs a new Fortnite\Model\ForniteLeaderboard instance.
     * @param array $stats   Array of mapped Leaderboard
     */
    public function __construct($data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case "rank":
                    $this->rank = $value;
                    break;
                case "accountId":
                    $this->accountid = $value;
                    break;
                case "value":
                    $this->score = $value;
                    break;
                case "displayName":
                    $this->displayname = $value;
                    break;
                default:
                    throw new InvalidStatException('Leaderboard key '. $key . ' is not supported');
            }
        }
    }


}
