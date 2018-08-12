<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Model\FortniteStats;
use Fortnite\Exception\UserNotFoundException;
use Fortnite\Exception\StatsNotFoundException;

use GuzzleHttp\Exception\GuzzleException;

class Account {

    public function __construct($access_token) {
        $this->access_token = $access_token;
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
}