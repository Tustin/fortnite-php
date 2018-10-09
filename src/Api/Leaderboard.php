<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Profile;
use Fortnite\Api\Leaderboard\Entry;

class Leaderboard extends AbstractApi {

    const LEADERBOARD_API   = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/leaderboards/type/global/stat/';
    const COHORT_API        = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/game/v2/leaderboards/cohort/';

    private $platform;
    private $mode; 
    
    private $info;

    public function __construct(Client $client, string $platform, string $mode) 
    {
        parent::__construct($client);
        
        $this->platform = $platform;
        $this->mode = $mode;
    }

    /**
     * Get the leaderboard information.
     *
     * @return object Leaderboard info.
     */
    public function info() : object
    {
        if ($this->info === null) {
            $cohort = $this->get(sprintf(self::COHORT_API . '%s', $this->client->inAppId()), [
                'playlist' => sprintf('%s_m0_%s', $this->platform(), $this->mode())
            ]);
    
            // Note (Tustin): itemsPerPage is hardcoded because I'm not sure what to pass to the cohort request above to get more than 50 account ids.
            // If someone knows, feel free to implement a $limit parameter to allow for a custom amount of leaderboard entries.
            $this->info = $this->postJson(
                sprintf(
                    self::LEADERBOARD_API . 'br_placetop1_%s_m0_%s/window/weekly?ownertype=1&itemsPerPage=50', $this->platform(), $this->mode()
                ),
                $cohort->cohortAccounts
            );
        }

        return $this->info;
    }

    /**
     * Get a list of each leaderboard entry.
     *
     * @return array Array of Api\Leaderboard\Entry.
     */
    public function entries() : array
    {
        $returnEntries = [];

        $entries = $this->info()->entries ?? [];

        if (empty($entries)) return $returnEntries;

        foreach ($entries as $entry) {
            $returnEntries[] = new Entry($this->client, $entry);
        }

        return $returnEntries;
    }

    /**
     * Get the leaderboard platform.
     *
     * @return string Platform.
     */
    public function platform() : string
    {
        return $this->platform;
    }

    /**
     * Get the leaderboard mode.
     * 
     * @return string Mode.
     */
    public function mode() : string
    {
        return $this->mode;
    }

}