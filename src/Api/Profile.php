<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Stat;
use Fortnite\Api\Platform;

class Profile extends AbstractApi {

    const FORTNITE_API  = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/';
    const PERSONA_API   = 'https://persona-public-service-prod06.ol.epicgames.com/persona/api/';


    private $profile;
    private $displayName;
    private $accountId;

    private $stats;
    public $challenges;

    public function __construct(Client $client, string $displayName, string $accountId = null) 
    {
        parent::__construct($client);

        $this->displayName = $displayName;

        if ($accountId != null) {
            $this->accountId = $accountId;
        } else {
            // If the current Profile isn't for the logged in user, we need to grab their id.
            if ($this->displayName() !== $client->displayName()) {
                $this->accountId = $this->lookup()->id;
            } else {
                $this->accountId = $client->accountId();
            }
        }
    }

    /**
     * Gets the user's display name.
     *
     * @return string The display name.
     */
    public function displayName() : string
    {
        return $this->displayName;
    }

    /**
     * Gets the user's account ID.
     *
     * @return string The account ID.
     */
    public function accountId() : string
    {
        return $this->accountId;
    }

    /**
     * Gets the user's profile information.
     *
     * @return object Profile information.
     */
    public function info() : object
    {
        if ($this->profile === null) {
            $response = $this->postJson(sprintf(self::FORTNITE_API . 'game/v2/profile/%s/client/QueryProfile?profileId=athena&rvn=-1', $this->accountId()), new \StdClass);

            $this->profile = $response->profileChanges[0]->profile;
        }

        return $this->profile;
    }

    /**
     * Gets the user's Items.
     *
     * @return array Array of Api\Item.
     */
    public function items() : array
    {
        $profile = $this->info();
        $returnItems = [];

        foreach ($profile->items as $key => $item) {
            if (substr($item->templateId, 0, strlen('Athena')) !== 'Athena') {
                continue;
            }

            $returnItems[] = new Item($this->client, $key, $item);
        }

        return $returnItems;
    }

    /**
     * Gets the user's PS4 stats.
     *
     * @return Platform PS4 stats.
     */
    public function ps4() : Platform
    {
        return new Platform($this->client, $this->parseStats('ps4'));
    }
    
    /**
     * Gets the user's Xbox One stats.
     *
     * @return Platform Xbox One stats.
     */
    public function xboxOne() : Platform
    {
        return new Platform($this->client, $this->parseStats('xb1'));
    }

    /**
     * Gets the user's PC stats.
     *
     * @return Platform PC stats.
     */
    public function pc() : Platform
    {
        return new Platform($this->client, $this->parseStats('pc'));
    }

    /**
     * Gets the user's stats raw information.
     *
     * @return array Raw stat information.
     */
    public function stats() : array
    {
        if ($this->stats === null) {
            $this->stats = $this->get(sprintf(self::FORTNITE_API . 'stats/accountId/%s/bulk/window/alltime', $this->accountId()));
        }

        return $this->stats;
    }


    /**
     * Adds the user as a friend.
     *
     * @return void
     */
    public function add() : void
    {
        $this->post(sprintf(Account::FRIENDS_API . '%s/%s', $this->client->accountId(), $this->accountId), []);
    }
    
    /**
     * Removes the user as a friend.
     *
     * @return void
     */
    public function remove() : void
    {
        $this->delete(sprintf(Account::FRIENDS_API . '%s/%s', $this->client->accountId(), $this->accountId));
    }

    /**
     * Parses the user's stats based on Platform.
     *
     * @param string $platform The Platform (ps4/xb1/pc)
     * @return array
     */
    private function parseStats(string $platform) : array 
    {
        $finalStats = [];
        foreach ($this->stats() as $stat) {
            $pieces = explode("_", $stat->name);
            if ($platform === $pieces[2]) {
                $finalStats[] = $stat;
            }
        }

        return $finalStats;

    }

    /**
     * Looks up the user's account information using the display name.
     * 
     * This is required for getting a user's account id so their stats can be searched.
     *
     * @return object User information.
     */
    private function lookup() : object
    {
        return $this->get(self::PERSONA_API . 'public/account/lookup', [
            'q' => $this->displayName()
        ]);
    }
}