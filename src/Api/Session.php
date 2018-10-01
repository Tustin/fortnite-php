<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Session extends AbstractApi {

    const MATCHMAKING_API = 'https://fortnite-public-service-prod-live-m.ol.epicgames.com/fortnite/api/matchmaking/';

    private $sessionId;
    private $session;

    /**
     * Api\Session is for a current matchmaking session.
     * Not completely mapped out, but you can var_dump($this->info()) for properties in the meantime.
     *
     * @param Client $client
     * @param string $sessionId The matchmaking session ID.
     */
    public function __construct(Client $client, string $sessionId) 
    {
        parent::__construct($client);

        $this->sessionId = $sessionId;
    }

    /**
     * Get information for the session.
     *
     * @return object Session information.
     */
    public function info() : object
    {
        if ($this->session === null) {
            $this->session = $this->get(sprintf(self::MATCHMAKING_API . 'session/%s', $this->sessionId));
        }
        return $this->session;
    }

}