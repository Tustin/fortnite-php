<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Profile;

class Account extends AbstractApi {

    const ACCOUNT_API   = 'https://account-public-service-prod03.ol.epicgames.com/account/api/';
    const FRIENDS_API   = 'https://friends-public-service-prod06.ol.epicgames.com/friends/api/public/friends/';

    public function __construct(Client $client) 
    {
        parent::__construct($client);
    }

    public function killSession() : void
    {
        $this->delete(self::ACCOUNT_API . 'oauth/sessions/kill', [
            'killType' => 'OTHERS_ACCOUNT_CLIENT_SERVICE'
        ]);
    }


    public function profile() : Profile
    {
        return new Profile($this->client, "");
    }

    public function friends() : array
    {
        $returnFriends = [];
        $friends = $this->get(self::FRIENDS_API . $this->client->accountId());

        foreach ($friends as $friend) {
            $accountInfo = $this->get(self::ACCOUNT_API . 'public/account', [
                'accountId' => $friend->accountId
            ])[0];

            // This can happen for some reason. Probably want to try to still return this user but for now, skip them.
            if (!isset($accountInfo->displayName)) {
                continue;
            }

            $returnFriends[] = new Profile($this->client, $accountInfo->displayName, $friend->accountId);
        }

        return $returnFriends;
    }
}