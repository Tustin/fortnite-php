<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Status extends AbstractApi {

    const STATUS_API = 'https://lightswitch-public-service-prod06.ol.epicgames.com/lightswitch/api/service/bulk/status?serviceId=Fortnite';

    private $status;

    public function __construct(Client $client) 
    {
        parent::__construct($client);
    }

    /**
     * Gets the status for Fortnite.
     *
     * @return object
     */
    public function info() : object
    {
        if ($this->session === null) {
            $this->session = $this->get(self::STATUS_API)[0];
        }
        return $this->session;
    }

    /**
     * Gets the status string for the game.
     *
     * @return string The status
     */
    public function status() : string
    {
        return $this->info()->status ?? "";
    }

    /**
     * Gets the status message.
     * 
     * @return string The message.
     */
    public function message() : string
    {
        return $this->info()->message ?? "";
    }

    /**
     * Gets the allowed actions for the status.
     * 
     * This indicates whether the game is playable, can be downloaded, etc.
     *
     * @return array Array of string.
     */
    public function allowedActions() : array
    {
        return $this->info()->allowedActions ?? [];
    }

    /**
     * Gets the maintenance URL (if maintenance is active).
     *
     * @return string|null The URL, or null if there is no maintenance.
     */
    public function maintenanceUri() : ?string 
    {
        return $this->info()->maintenanceUri;
    }

}