<?php

namespace Fortnite;


class Store
{
    private $access_token;
    private $lang;

    public function __construct($access_token) {
        $this->access_token = $access_token;
    }

    public function getStore($lang)
    {
        if ($lang == Language::CHINESE && $lang == Language::JAPANESE)
            throw Exception("The language you're trying to use is currently not supported in the fortnite store");
        if ($lang != Language::ENGLISH && $lang != Language::GERMAN && $lang != Language::SPANISH  && $lang != Language::FRENCH && $lang != Language::FRENCH && $lang != Language::ITALIEN && $lang != Language::JAPANESE)
            throw Exception("Unknown Language");

        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_API . 'storefront/v2/catalog',
                $this->access_token, array('X-EpicGames-Language' => $lang));
            return $data;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new Exception('Unable to obtain store info.');
            throw $e; //If we didn't get the user not found status code, just re-throw the error.
        }
    }
}