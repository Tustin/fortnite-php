<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Language;

use GuzzleHttp\Exception\GuzzleException;

class Store
{
    private $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
    }

    public function get($lang = Language::ENGLISH)
    {
        if ($lang === Language::CHINESE && $lang === Language::JAPANESE)
            throw new \Exception("The language you're trying to use is currently not supported in the Fortnite store");
        
        // TODO: cleanup
        if ($lang != Language::ENGLISH 
            && $lang !== Language::GERMAN 
            && $lang !== Language::SPANISH  
            && $lang !== Language::FRENCH 
            && $lang !== Language::FRENCH 
            && $lang !== Language::ITALIAN 
            && $lang !== Language::JAPANESE)
                throw new \Exception("Unknown Language");

        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_API . 'storefront/v2/catalog',
                $this->access_token, ['X-EpicGames-Language' => $lang]);
            return $data;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new \Exception('Unable to obtain store info.');
            throw $e;
        }
    }
}