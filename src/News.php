<?php

namespace Fortnite;

use Fortnite;
use Fortnite\Model\FortniteNews;
use Fortnite\NewsType;


class News
{
    private $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
    }


    public function getNews($lang, $type)
    {
        if ($lang != Language::ENGLISH && $lang != Language::GERMAN && $lang != Language::SPANISH  && $lang != Language::CHINESE && $lang != Language::FRENCH && $lang != Language::FRENCH && $lang != Language::ITALIEN && $lang != Language::JAPANESE)
            throw Exception("Unknown Language");
        if ($type != NewsType::SAVETHEWORLD && $type != NewsType::BATTLEROYALE)
            throw Exception("Only SaveTheWorld and BattleRoyale news are currently supported");


        try {
            $data = FortniteClient::sendFortniteGetRequest(FortniteClient::FORTNITE_NEWS_API . 'pages/fortnite-game',
                $this->access_token, array('Accept-Language' => $lang));

            $data = $data->$type->news->messages;

            $news = [];
            foreach ($data as  $key => $stat) {
                    $news[$key] = new FortniteNews($stat);
            }

            return $news;
        } catch (GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() == 404) throw new Exception('Unable to obtain news.');
            throw $e; //If we didn't get the user not found status code, just re-throw the error.
        }
    }

}