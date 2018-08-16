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

    public function stats() : ?array
    {
        return $this->sortedStats;
    }

    public function wins() : int
    {
        return $this->stat('placetop1')->value ?? 0;
    }

    public function top3() : int
    {
        return $this->stat('placetop3')->value ?? 0;
    }

    public function top5() : int
    {
        return $this->stat('placetop5')->value ?? 0;
    }

    public function top6() : int
    {
        return $this->stat('placetop6')->value ?? 0;
    }

    public function top10() : int
    {
        return $this->stat('placetop10')->value ?? 0;
    }

    public function top12() : int
    {
        return $this->stat('placetop12')->value ?? 0;
    }

    public function top25() : int
    {
        return $this->stat('placetop25')->value ?? 0;
    }
    
    public function kills() : int
    {
        return $this->stat('kills')->value ?? 0;
    }

    public function score() : int // Possibly could overflow if PHP_INT_MAX is 32 bit
    {
        return $this->stat('score')->value ?? 0;
    }

    public function minutesPlayed() : int
    {
        return $this->stat('minutesplayed')->value ?? 0;
    }

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