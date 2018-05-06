<?php

namespace Fortnite;

use Fortnite;

class Status
{
    private $access_token;

    public function __construct($access_token)
    {
        $this->access_token = $access_token;
    }


    public function getStatus()
    {
        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_STATUS_API . 'service/bulk/status?serviceId=Fortnite',
                $this->access_token);

            return $data[0]->status;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new Exception('Unable to obtain Fortnite status.');
            throw $e;
        }
    }
}