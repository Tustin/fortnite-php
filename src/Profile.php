<?php
namespace Fortnite;

use Fortnite\FortniteClient;

class Profile {
    private $access_token;
    private $account_id;

    public $stats;

    public function __construct($access_token, $account_id) {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
        $this->stats = new Stats($access_token, $account_id);
    }

    // Do something better with this. Maybe only extract useful data?
    private function fetch() {
        $data = FortniteClient::sendFortnitePostRequest('game/v2/profile/' . $this->account_id . '/client/QueryProfile?profileId=profile0&rvn=-1',
                                                        $this->access_token,
                                                        new \StdClass());
        return $data;
    }

    public function getFriends() {
        $data = FortniteClient::sendUnrealClientGetRequest(FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id, $this->access_token, true);

        return $data;
    }
}