<?php
namespace Fortnite\Api\Stats;

use Fortnite\Client;

use Fortnite\Api\AbstractApi;

use Fortnite\Api\Type\Mode as ModeType;

class Platform extends AbstractApi {

    private $stats;

    public function __construct(Client $client, array $stats) 
    {
        parent::__construct($client);

        $this->stats = $stats;
    }

    /**
     * Gets the stats data.
     *
     * @return array
     */
    public function stats() : array
    {
        return $this->stats;
    }

    /**
     * Gets solo stats.
     *
     * @return Mode The solo stats.
     */
    public function solo() : Mode
    {
        return new Mode($this->client, $this->mode(ModeType::SOLO));
    }

    /**
     * Gets duo stats.
     *
     * @return Mode The duo stats.
     */
    public function duo() : Mode
    {
        return new Mode($this->client, $this->mode(ModeType::DUO));
    }

    /**
     * Gets squad stats.
     *
     * @return Mode The squad stats.
     */
    public function squad() : Mode
    {
        return new Mode($this->client, $this->mode(ModeType::SQUAD));
    }

    /**
     * Gets stats based on mode name.
     *
     * @param string $mode The mode id. @see Api\Type\Mode
     * @return array Stats.
     */
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