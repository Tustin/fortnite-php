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

    public function __construct(Client $client, string $displayName) 
    {
        parent::__construct($client);

        $this->displayName = $displayName;

        // If the current Profile isn't for the logged in user, we need to grab their id.
        if ($this->displayName() !== $client->displayName()) {
            $this->accountId = $this->lookup()->id;
        } else {
            $this->accountId = $client->accountId();
        }
    }

    public function displayName() : string
    {
        return $this->displayName;
    }

    public function accountId() : string
    {
        return $this->accountId;
    }

    private function lookup() : object
    {
        return $this->get(self::PERSONA_API . 'public/account/lookup', [
            'q' => $this->displayName()
        ]);
    }

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

    public function ps4() : Platform
    {
        return new Platform($this->client, $this->parseStats('ps4'));
    }

    public function stats() : array
    {
        if ($this->stats === null) {
            $this->stats = $this->get(sprintf(self::FORTNITE_API . 'stats/accountId/%s/bulk/window/alltime', $this->accountId()));
        }

        return $this->stats;
    }

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
}