<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Mode extends AbstractApi {

    private $stats;
    private $sortedStats;

    public function __construct(Client $client, array $stats) 
    {
        parent::__construct($client);

        $this->stats = $stats;
    }

    /**
     * Gets the stats data.
     *
     * @return array|null Stats data.
     */
    public function stats() : ?array
    {
        return $this->sortedStats;
    }

    /**
     * Gets the amount of wins.
     *
     * @return integer Wins.
     */
    public function wins() : int
    {
        return $this->stat('placetop1')->value ?? 0;
    }

    /**
     * Gets the amounf of top 5 finishes.
     *
     * @return integer Top 3 finishes.
     */
    public function top3() : int
    {
        return $this->stat('placetop3')->value ?? 0;
    }

    /**
     * Gets the amount of top 5 finishes.
     *
     * @return integer Top 5 finishes.
     */
    public function top5() : int
    {
        return $this->stat('placetop5')->value ?? 0;
    }

    /**
     * Gets the amount of top 6 finishes.
     *
     * @return integer Top 6 finishes.
     */
    public function top6() : int
    {
        return $this->stat('placetop6')->value ?? 0;
    }

    /**
     * Gets the amount of top 10 finishes.
     *
     * @return integer Top 10 finishes.
     */
    public function top10() : int
    {
        return $this->stat('placetop10')->value ?? 0;
    }
    
    /**
     * Gets the amount of top 12 finishes.
     *
     * @return integer Top 12 finishes.
     */
    public function top12() : int
    {
        return $this->stat('placetop12')->value ?? 0;
    }

    /**
     * Gets the amount of top 25 finishes.
     *
     * @return integer Top 25 finishes.
     */
    public function top25() : int
    {
        return $this->stat('placetop25')->value ?? 0;
    }
    
    /**
     * Gets the amount of kills.
     *
     * @return integer Kills.
     */
    public function kills() : int
    {
        return $this->stat('kills')->value ?? 0;
    }

    /**
     * Gets the amount of score.
     *
     * @return integer Score
     */
    public function score() : int
    {
        return $this->stat('score')->value ?? 0;
    }

    /**
     * Gets the total minutes played
     * 
     * TODO (Tustin): Maybe return this as a DateTime?
     *
     * @return integer Minutes played.
     */
    public function minutesPlayed() : int
    {
        return $this->stat('minutesplayed')->value ?? 0;
    }

    /**
     * Gets a stat value based on it's name.
     *
     * @param string $statName The stat name.
     * @return object|null The stat information.
     */
    private function stat(string $statName) : ?object
    {
        if ($this->stats() === null) {
            foreach ($this->stats as $stat) {
                $pieces = explode('_', $stat->name);
                $this->sortedStats[$pieces[1]] = $stat;
            }
        }
        return $this->sortedStats[$statName] ?? null; // Suppress warnings
    }
}