<?php
namespace Fortnite\Api;

use Fortnite\Client;

class Message extends AbstractApi {

    private $message;

    public function __construct(Client $client, object $message) 
    {
        parent::__construct($client);

        $this->message = $message;
    }

    /**
     * Get News Message information.
     *
     * @return object
     */
    public function info() : object
    {
        return $this->message;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function title() : string
    {
        return $this->info()->title;
    }

     /**
      * Get body.
      *
      * @return string
      */
    public function body() : string
    {
        return $this->info()->body;
    }

    /**
     * Check if Message is hidden.
     *
     * @return boolean
     */
    public function hidden() : bool
    {
        return $this->info()->hidden;
    }

    /**
     * Check if Message is in the spotlight.
     *
     * @return boolean
     */
    public function spotlight() : bool
    {
        return $this->info()->spotlight;
    }

}