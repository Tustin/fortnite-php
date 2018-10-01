<?php

namespace Fortnite;

use Fortnite\Http\HttpClient;
use Fortnite\Http\ResponseParser;
use Fortnite\Http\TokenMiddleware;
use Fortnite\Http\FortniteAuthMiddleware;

use Fortnite\Api\Account;
use Fortnite\Api\Profile;
use Fortnite\Api\SystemFile;
use Fortnite\Api\News;
use Fortnite\Api\Store;
use Fortnite\Api\Leaderboard;

use GuzzleHttp\Middleware;

class Client {

    const EPIC_ACCOUNT_ENDPOINT         = 'https://account-public-service-prod03.ol.epicgames.com/account/api/';
    const EPIC_OAUTH_EXCHANGE_ENDPOINT  = 'https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/exchange';
    const EPIC_OAUTH_VERIFY_ENDPOINT    = 'https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/verify';
    const EPIC_FRIENDS_ENDPOINT         = 'https://friends-public-service-prod06.ol.epicgames.com/friends/api/public/friends/';

    private $httpClient;
    private $options;

    private $accountId;
    private $accountInfo;

    private $in_app_id;

    public function __construct($options = [])
    {
        $this->options = $options;
        $handler = \GuzzleHttp\HandlerStack::create();
        $handler->push(Middleware::mapRequest(new FortniteAuthMiddleware));

        $newOptions = array_merge(['handler' => $handler], $this->options);

        $this->httpClient = new HttpClient(new \GuzzleHttp\Client($newOptions));
    }
    
    /**
     * Login to Fortnite using Epic email and password.
     *
     * @param string $email
     * @param string $password
     * @return void
     */
    public function login(string $email, string $password) 
    {
        // Get our Epic Launcher authorization token.
        $response = $this->httpClient()->post(self::EPIC_ACCOUNT_ENDPOINT . 'oauth/token', [
            'grant_type'    => 'password',
            'username'      => $email,
            'password'      => $password,
            'includePerms'  => 'false',
            'token_type'    => 'eg1'
        ]);

        // TODO: 2FA checking here
        $this->accountId = $response->account_id;

        $handler = \GuzzleHttp\HandlerStack::create();
        $handler->push(Middleware::mapRequest(new TokenMiddleware($response->access_token, $response->refresh_token, $response->expires_in)));

        $this->in_app_id = $response->in_app_id ?? "";

        $newOptions = array_merge(['handler' => $handler], $this->options);

        $this->httpClient = new HttpClient(new \GuzzleHttp\Client($newOptions));
    }

    /**
     * Gets the HttpClient.
     *
     * @return HttpClient
     */
    public function httpClient() : HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Get the user's account ID.
     *
     * @return string
     */
    public function accountId() : string
    {
        return $this->accountId;
    }

    public function inAppId() : string
    {
        return $this->in_app_id;
    }

    /**
     * Gets the user's Epic display name
     *
     * @return string
     */
    public function displayName() : string
    {
        return $this->accountInfo()->displayName;
    }

    public function accountInfo() : object
    {
        if ($this->accountInfo === null) {
            $this->accountInfo = $this->httpClient()->get(sprintf(self::EPIC_ACCOUNT_ENDPOINT . 'public/account/%s', $this->accountId()));
        }

        return $this->accountInfo;
    }

    public function account() : Account
    {
        return new Account($this);
    }

    public function profile(string $username = null) : Profile
    {
        return new Profile($this, $username ?? $this->displayName());
    }

    public function session(string $sessionId) : Session
    {
        return new Session($this, $sessionId);
    }

    public function leaderboards(string $platform, string $mode) : Leaderboard
    {
        return new Leaderboard($this, $platform, $mode);
    }

    public function systemFiles() : array
    {
        $returnSystemFiles = [];
        
        $files = $this->httpClient()->get(SystemFile::SYSTEM_API);

        foreach ($files as $file) {
            $returnSystemFiles[] = new SystemFile($this, $file);
        }

        return $returnSystemFiles;
    }

    public function news() : News
    {
        return new News($this);
    }

    /**
     * Gets the Store Front.
     *
     * @return Store
     */
    public function store() : Store
    {
        return new Store($this);
    }
}