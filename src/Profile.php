<?php
namespace Fortnite;

use Fortnite\FortniteClient;

use Fortnite\Model\Items;

class Profile {
    private $access_token;
    private $account_id;

    public $stats;
    public $items;
    public $challenges;

    /**
     * Constructs a new Fortnite\Profile instance.
     * @param string $access_token OAuth2 Access token
     * @param string $account_id   Epic account id
     */
    public function __construct($access_token, $account_id) {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
        $data = $this->fetch();
        $this->items = new Items($data->items);
        $this->stats = new Stats($access_token, $account_id);
        $this->challenges = new Challenges($this->access_token, $data->items);

    }

    /**
     * Fetches profile data.
     * @return object Profile data
     */
    private function fetch() {
        $data = FortniteClient::sendFortnitePostRequest(FortniteClient::FORTNITE_API . 'game/v2/profile/' . $this->account_id . '/client/QueryProfile?profileId=athena&rvn=-1',
                                                        $this->access_token,
                                                        new \StdClass());
        return $data->profileChanges[0]->profile;
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