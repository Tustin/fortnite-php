<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Session extends AbstractApi {

    const MATCHMAKING_API = 'https://fortnite-public-service-prod-live-m.ol.epicgames.com/fortnite/api/matchmaking/';

    private $sessionId;
    private $session;

    public function __construct(Client $client, string $sessionId) 
    {
        parent::__construct($client);

        $this->sessionId = $sessionId;
    }

    public function info() : object
    {
        if ($this->session === null) {
            $this->session = $this->get(sprintf(self::MATCHMAKING_API . 'session/%s', $this->sessionId));
        }
        return $this->session;
    }

}