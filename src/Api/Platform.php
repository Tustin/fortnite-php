<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Platform extends AbstractApi {

    private $stats;

    public function __construct(Client $client, array $stats) 
    {
        parent::__construct($client);

        $this->stats = $stats;
    }

    public function stats() : array
    {
        return $this->stats;
    }

    public function solo() : Mode
    {
        return new Mode($this->client, $this->mode('p2'));
    }

    public function duo() : Mode
    {
        return new Mode($this->client, $this->mode('p10'));
    }

    public function squad() : Mode
    {
        return new Mode($this->client, $this->mode('p9'));
    }

    private function mode(string $mode) : array
    {
        $returnMode = [];
        foreach ($this->stats() as $stat) {
            $pieces = explode('_', $stat->name);
            if ($pieces[4] === $mode) {
                $returnMode[] = $stat;
            }
        }

        return $returnMode;
    }

}