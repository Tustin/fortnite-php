<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Profile;

class Account extends AbstractApi {

    const ACCOUNT_API          = "https://account-public-service-prod03.ol.epicgames.com/account/api/";

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

    public function friends() {
        // $data = FortniteClient::sendUnrealClientGetRequest(FortniteClient::EPIC_FRIENDS_ENDPOINT . $this->account_id, $this->access_token, true);

        // return $data;
    }
}