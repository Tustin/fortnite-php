<?php
namespace Fortnite;

use Fortnite\FortniteClient;

class Stats {
    private $access_token;
    private $account_id;

    public function __construct($access_token, $account_id) {
        $this->access_token = $access_token;
        $this->account_id = $account_id;
    }

    public function fetch() {
        $data = FortniteClient::sendFortniteGetRequest('stats/accountId/' . $this->account_id . '/bulk/window/alltime',
                                                        $this->access_token);

        return $data;
    }
}