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

    /**
     * Fetches current profile data
     * @param  string $profile_id Profile Id to get data for. Unsure what this is used for.
     * @return object             The profile's data
     */
    private function fetch($profile_id = "profile0") {
        $data = FortniteClient::sendFortnitePostRequest(FortniteClient::FORTNITE_API . 'game/v2/profile/' . $this->account_id . '/client/QueryProfile?profileId=profile0&rvn=-1',
                                                        $this->access_token,
                                                        new \StdClass());
        return $data;
    }

    /**
     * Get current user's friends on Unreal Engine.
     * @return array    Array of friends
     */
    public function getFriends() {
        $data = FortniteClient::sendUnrealClientGetRequest(FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id, $this->access_token, true);

        return $data;
    }
}