<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Model\FortniteStats;
use Fortnite\Exception\UserNotFoundException;
use Fortnite\Exception\StatsNotFoundException;

use GuzzleHttp\Exception\GuzzleException;

class Account {

    private $account_id;

    public function __construct($access_token,$account_id) {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
    }

    public static function getDisplayNameFromID($id, $access_token) {
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_ACCOUNT_API . "public/account?accountId={$id}",
                $access_token);

            return $data[0]->displayName;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new Exception('Could not get display name of account id ' . $id);
            throw $e;
        }
    }

    public function getDisplayNamesFromID($id) {
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_ACCOUNT_API . "public/account?accountId=" . join('&accountId=', $id),
                $this->access_token);

            return $data;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new \Exception('Could not get display name of account id ' . $id);
            throw $e;
        }
    }

    public function killSession() {
        FortniteClient::sendFortniteDeleteRequest(FortniteClient::FORTNITE_ACCOUNT_API . "oauth/sessions/kill/" . $this->access_token, $this->access_token);
    }

    public function acceptEULA(){
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_EULA_API . "public/agreements/fn/account/" . $this->account_id .'?locale=en-US',
                $this->access_token);

            FortniteClient::sendFortnitePostRequest(FortniteClient::FORTNITE_EULA_API . "public/agreements/fn/version/".$data->version."/account/".$this->account_id."/accept?locale=en",
                $this->access_token,new \StdClass());

            FortniteClient::sendFortnitePostRequest(FortniteClient::FORTNITE_API.'game/v2/grant_access/'.$this->account_id,
                $this->access_token,new \StdClass());

            return true;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new \Exception('Could not read or accept EULA for account id ' . $this->account_id);
            throw $e;
        }
    }
}