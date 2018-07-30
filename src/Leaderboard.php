<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Platform;
use Fortnite\Mode;

use Fortnite\Model\FortniteLeaderboard;


use Fortnite\Exception\UserNotFoundException;
use Fortnite\Exception\StatsNotFoundException;

use GuzzleHttp\Exception\GuzzleException;

class Leaderboard
{
    private $access_token;
    private $account;

    public function __construct($access_token, Account $account)
    {
        $this->account = $account;
        $this->access_token = $access_token;
    }

    /**
     * Get leaderboard (top 50)
     * @param  string $platform (PC, PS4, XB1)
     * @param  string $type (SOLO,DUO, SQUAD)
     * @return object           New instance of Fortnite\Leaderboard
     */
    public function get($platform, $type)
    {
        if ($platform !== Platform::PC 
            && $platform !== Platform::PS4 
            && $platform !== Platform::XBOX1)
                throw new \Exception('Please select a platform');
      
        if ($type !== Mode::DUO
            && $type !== Mode::SOLO
            && $type !== Mode::SQUAD) {
            throw new \Exception('Please select a game mode');
        }

        try {
            $data = FortniteClient::sendFortnitePostRequest(
                FortniteClient::FORTNITE_API . "leaderboards/type/global/stat/br_placetop1_{$platform}_m0{$type}/window/weekly?ownertype=1&itemsPerPage=50",
                $this->access_token
            );
            $entries = $data->entries;


            $ids = array();
            foreach ($entries as $entry) {
                $entry->accountId = str_replace("-", "", $entry->accountId);
                array_push($ids, $entry->accountId);
            }

            $accounts = $this->account->getDisplayNamesFromID($ids);

            foreach ($entries as $entry) {
                foreach ($accounts as $account) {
                    if ($entry->accountId === $account->id) {
                        $entry->displayName = $account->displayName ?? null;
                        break;
                    }
                }
            }

            $leaderboard = [];
            foreach ($entries as $key => $stat) {
                $leaderboard[$key] = new FortniteLeaderboard($stat);
            }

            return $leaderboard;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                throw new LeaderboardNotFoundException('Could not get leaderboards.');
            }
            throw $e;
        }
    }
}
