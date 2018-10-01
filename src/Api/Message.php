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
     * @return object Message info.
     */
    public function info() : object
    {
        return $this->message;
    }

    /**
     * Get title.
     *
     * @return string Title.
     */
    public function title() : string
    {
        return $this->info()->title;
    }

     /**
      * Get body.
      *
      * @return string Body.
      */
    public function body() : string
    {
        return $this->info()->body;
    }

    /**
     * Check if Message is hidden.
     *
     * @return boolean Is hidden?
     */
    public function hidden() : bool
    {
        return $this->info()->hidden;
    }

    /**
     * Check if Message is in the spotlight.
     *
     * @return boolean Is spotlight?
     */
    public function spotlight() : bool
    {
        return $this->info()->spotlight;
    }

}