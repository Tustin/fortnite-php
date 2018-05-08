<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Account;

use Fortnite\Model\FortniteStats;

use Fortnite\Exception\UserNotFoundException;
use Fortnite\Exception\StatsNotFoundException;

use GuzzleHttp\Exception\GuzzleException;

class Stats {
    private $access_token;
    public $account_id;
    public $display_name;

    public $ps4;
    public $pc;
    public $xb1;

    /**
     * Constructs a new Fortnite\Stats instance.
     * @param string $access_token OAuth2 Access token
     * @param string $account_id   Epic account id
     */
    public function __construct($access_token, $account_id) {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
        $data = $this->fetch($this->account_id);
        if (array_key_exists("ps4", $data)) $this->ps4 = $data["ps4"];
        if (array_key_exists("pc", $data)) $this->pc = $data["pc"];
        if (array_key_exists("xb1", $data)) $this->xb1 = $data["xb1"];
    }

    /**
     * Fetches stats for the current user.
     * @param  string $account_id   Account id
     * @return object               The stats data
     */
    private function fetch($account_id) {
        if (!$account_id) return null;

        $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_API . 'stats/accountId/' . $account_id . '/bulk/window/alltime',
                                                     $this->access_token);

        // Remove - from account ID and get it's display name
        $this->display_name = Account::getDisplayNameFromID(str_replace("-","",$this->account_id), $this->access_token);
        //if (!count($data)) throw new StatsNotFoundException('Unable to find any stats for account id '. $account_id);

        // Loop over all the stat objects and compile them together cleanly
        $compiledStats = [];
        foreach ($data as $stat) {
            $parsed = $this->parseStatItem($stat);
            $compiledStats = array_merge_recursive($compiledStats, $parsed);
        }

        // Now loop over the compiled stats and create proper objects
        $platforms = [];
        foreach ($compiledStats as $key => $platform) {
            $platforms[$key] = new Platform($platform);
        }

        return $platforms;
    }

    /**
     * Lookup a user by their Epic display name.
     * @param  string $username Display name to search
     * @return object           New instance of Fortnite\Stats
     */
    public function lookup($username) {
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_PERSONA_API . 'public/account/lookup?q=' . urlencode($username),
                                                        $this->access_token);
            return new self($this->access_token, $data->id);
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new UserNotFoundException('User ' . $username . ' was not found.');
            throw $e; //If we didn't get the user not found status code, just re-throw the error.
        }
    }

    //TODO (Tustin): Make this not redundant
    public static function lookupWithToken($username, $access_token) {
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_PERSONA_API . 'public/account/lookup?q=' . urlencode($username),
                                                        $access_token);
            return new self($access_token, $data->id);
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new UserNotFoundException('User ' . $username . ' was not found.');
            throw $e; //If we didn't get the user not found status code, just re-throw the error.
        }
    }

    /**
     * Parses a stat string into a mapped array.
     * @param  string $stat The stat string
     * @return array        The mapped stat array
     */
    private function parseStatItem($stat): array {
        //
        // Example stat name:
        // br_placetop5_ps4_m0_p10
        // {type}_{name}_{platform}_{??}_{mode (squads/solo/duo)}
        // 
        $result = [];
        $pieces = explode("_", $stat->name);
        $result[$pieces[2]][$pieces[4]][$pieces[1]] = $stat->value;
        return $result;
    }

    public function accountId() {
        return $this->account_id;
    }

}