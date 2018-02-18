<?php
namespace Fortnite;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FortniteClient {
    // Unsure if this changes between client updates or what. It's a base64 encoded string which contains two MD5 hashes delimited by a colon.
    // The hashes are derived before any authentication so it might be a checksum of some file?
    const EPIC_LAUNCHER_AUTHORIZATION   = "MzRhMDJjZjhmNDQxNGUyOWIxNTkyMTg3NmRhMzZmOWE6ZGFhZmJjY2M3Mzc3NDUwMzlkZmZlNTNkOTRmYzc2Y2Y=";

    // Same format as the EPIC_LAUNCHER_AUTHORIZATION. Also unsure of it's origin.
    const FORTNITE_AUTHORIZATION        = "ZWM2ODRiOGM2ODdmNDc5ZmFkZWEzY2IyYWQ4M2Y1YzY6ZTFmMzFjMjExZjI4NDEzMTg2MjYyZDM3YTEzZmM4NGQ=";


    //
    // API Endpoints
    //

    const EPIC_OAUTH_TOKEN_ENDPOINT     = "https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/token";
    const EPIC_OAUTH_EXCHANGE_ENDPOINT  = "https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/exchange";
    const EPIC_FRIENDS_ENDPOINT         = "https://friends-public-service-prod06.ol.epicgames.com/friends/api/public/friends/";

    const FORTNITE_API                  = "https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/";


    public static function sendUnrealClientGetRequest($endpoint, $authorization = self::EPIC_LAUNCHER_AUTHORIZATION, $oauth = false) {
        $client = new Client();

        $response = $client->get($endpoint, [
            'headers' => [
                'User-Agent' => 'game=UELauncher, engine=UE4, build=7.3.1-3881656+++Portal+Release-Live',
                'Authorization' => (!$oauth) ? 'basic ' . $authorization : 'bearer ' . $authorization
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public static function sendUnrealClientPostRequest($endpoint, $params = null, $authorization = self::EPIC_LAUNCHER_AUTHORIZATION, $oauth = false) {
        $client = new Client();

        $response = $client->post($endpoint, [
            'form_params' => $params,
            'headers' => [
                'User-Agent' => 'game=UELauncher, engine=UE4, build=7.3.1-3881656+++Portal+Release-Live',
                'Authorization' => (!$oauth) ? 'basic ' . $authorization : 'bearer ' . $authorization
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public static function sendFortniteGetRequest($endpoint, $access_token) {
        $client = new Client();

        $response = $client->get(self::FORTNITE_API . $endpoint, [
            'headers' => [
                'User-Agent' => 'game=Fortnite, engine=UE4, build=++Fortnite+Release-2.5-CL-3889387, netver=3886413',
                'Authorization' => 'bearer ' . $access_token
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public static function sendFortnitePostRequest($endpoint, $access_token, $params = null) {
        $client = new Client(['proxy' => '127.0.0.1:8888', 'verify' => false]);

        $response = $client->post(self::FORTNITE_API . $endpoint, [
            'json' => $params,
            'headers' => [
                'User-Agent' => 'game=Fortnite, engine=UE4, build=++Fortnite+Release-2.5-CL-3889387, netver=3886413',
                'Authorization' => 'bearer ' . $access_token
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}