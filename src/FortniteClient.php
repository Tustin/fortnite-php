<?php
namespace Fortnite;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FortniteClient {
    /**
     * base64 encoded string of two MD5 hashes delimited by a colon. The two hashes are the client_id and client_secret OAuth2 fields.
     */
    const EPIC_LAUNCHER_AUTHORIZATION   = "MzRhMDJjZjhmNDQxNGUyOWIxNTkyMTg3NmRhMzZmOWE6ZGFhZmJjY2M3Mzc3NDUwMzlkZmZlNTNkOTRmYzc2Y2Y=";

    
    /**
     * Same as EPIC_LAUNCHER_AUTHORIZATION
     */
    const FORTNITE_AUTHORIZATION        = "ZWM2ODRiOGM2ODdmNDc5ZmFkZWEzY2IyYWQ4M2Y1YzY6ZTFmMzFjMjExZjI4NDEzMTg2MjYyZDM3YTEzZmM4NGQ=";



    /**
     * Epic API Endpoints
     */
    const EPIC_OAUTH_TOKEN_ENDPOINT     = "https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/token";
    const EPIC_OAUTH_EXCHANGE_ENDPOINT  = "https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/exchange";
    const EPIC_OAUTH_VERIFY_ENDPOINT    = "https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/verify";
    const EPIC_FRIENDS_ENDPOINT         = "https://friends-public-service-prod06.ol.epicgames.com/friends/api/public/friends/";

    /**
     * Fortnite API Endpoints
     */
    const FORTNITE_API                  = "https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/";
    const FORTNITE_PERSONA_API          = "https://persona-public-service-prod06.ol.epicgames.com/persona/api/";
    const FORTNITE_ACCOUNT_API          = "https://account-public-service-prod03.ol.epicgames.com/account/api/";
    const FORTNITE_NEWS_API             = "https://fortnitecontent-website-prod07.ol.epicgames.com/content/api/";
    const FORTNITE_STATUS_API           = "https://lightswitch-public-service-prod06.ol.epicgames.com/lightswitch/api/";




    /**
     * Sends a GET request as the Unreal Engine Client.
     * @param  string  $endpoint      API Endpoint to request
     * @param  string  $authorization Authorization header
     * @param  boolean $oauth         Is $authorization an OAuth2 token
     * @return object                 Decoded JSON response body
     */
    public static function sendUnrealClientGetRequest($endpoint, $authorization = self::EPIC_LAUNCHER_AUTHORIZATION, $oauth = false) {
        $client = new Client();

        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'User-Agent' => 'game=UELauncher, engine=UE4, build=7.3.1-3881656+++Portal+Release-Live',
                    'Authorization' => (!$oauth) ? 'basic ' . $authorization : 'bearer ' . $authorization
                ]
            ]);
            
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw $e; //Throw exception back up to caller
        }
    }

    /**
     * Sends a POST request as the Unreal Engine Client.
     * @param  string  $endpoint      API Endpoint to request
     * @param  array   $params        Request parameters, using the name as the array key and value as the array value
     * @param  string  $authorization Authorization header
     * @param  boolean $oauth         Is $authorization an OAuth2 token
     * @return object                 Decoded JSON response body
     */
    public static function sendUnrealClientPostRequest($endpoint, $params = null, $authorization = self::EPIC_LAUNCHER_AUTHORIZATION, $oauth = false) {
        $client = new Client();

        try {
            $response = $client->post($endpoint, [
                'form_params' => $params,
                'headers' => [
                    'User-Agent' => 'game=UELauncher, engine=UE4, build=7.3.1-3881656+++Portal+Release-Live',
                    'Authorization' => (!$oauth) ? 'basic ' . $authorization : 'bearer ' . $authorization
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw $e; //Throw exception back up to caller
        }
    }

    /**
     * Sends a GET request as the Fortnite client.
     * @param  string $endpoint     API endpoint to request
     * @param  string $access_token OAuth2 access token
     * @param  array  $extra_headers (optional)
     * @return object               Decoded JSON response body
     */
    public static function sendFortniteGetRequest($endpoint, $access_token, $extra_headers = array()) {
        $client = new Client();

        $headers = [
            'User-Agent' => 'game=Fortnite, engine=UE4, build=++Fortnite+Release-2.5-CL-3889387, netver=3886413',
            'Authorization' => 'bearer ' . $access_token
        ];

        $headers = array_merge($headers, $extra_headers);
        try {
            $response = $client->get($endpoint, [
                'headers' => $headers
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw $e; //Throw exception back up to caller
        }
    }


    /**
     * Sends a POST request as the Fortnite client.
     * @param  string $endpoint     API endpoint to request
     * @param  string $access_token OAuth2 access token
     * @param  array  $params       Request parameters, using the name as the array key and value as the array value
     * @return object               Decoded JSON response body
     */
    public static function sendFortnitePostRequest($endpoint, $access_token, $params = null) {
        $client = new Client();

        try {
             $response = $client->post($endpoint, [
                'json' => $params,
                'headers' => [
                    'User-Agent' => 'game=Fortnite, engine=UE4, build=++Fortnite+Release-2.5-CL-3889387, netver=3886413',
                    'Authorization' => 'bearer ' . $access_token
                ]
            ]);

            return json_decode($response->getBody()->getContents());      
        } catch (GuzzleException$e) {
            throw $e; //Throw exception back up to caller
        }

    }
}