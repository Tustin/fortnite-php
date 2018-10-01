<?php
namespace Fortnite\Api\Leaderboard;

use Fortnite\Client;
use Fortnite\Api\AbstractApi;

use Fortnite\Api\Profile;

class Entry extends AbstractApi {

    private $info;

    public function __construct(Client $client, object $entry) 
    {
        parent::__construct($client);

        $this->info = $entry;
    }

    /**
     * Return leaderboard entry info.
     *
     * @return object Entry info.
     */
    public function info() : object 
    {
        return $this->info;
    }

    /**
     * Get the entry user's account ID.
     * 
     * @return string Account ID.
     */
    public function accountId() : string
    {
        return $this->info()->accountId;
    }

    /**
     * Get entry value.
     *
     * @return mixed Entry value.
     */
    public function value()
    {
        return $this->info()->value;
    }

    /**
     * Get leaderboard rank.
     *
     * @return integer Rank.
     */
    public function rank() : int
    {
        return $this->info()->rank;
    }

    /**
     * Get the user's profile.
     *
     * @return Profile User's profile.
     */
    public function profile() : Profile
    {
        return new Profile($this->client, '', $this->accountId());
    }
}