<?php

namespace Fortnite;


use Fortnite\Api\Account;
use Fortnite\Api\Profile;
use Fortnite\Api\SystemFile;
use Fortnite\Api\News;
use Fortnite\Api\Store;
use Fortnite\Api\Leaderboard;
use Fortnite\Api\Status;

use Fortnite\Api\Exception\TwoFactorRequiredException;

use Fortnite\Http\HttpClient;
use Fortnite\Http\ResponseParser;
use Fortnite\Http\TokenMiddleware;
use Fortnite\Http\FortniteAuthMiddleware;

use Fortnite\Http\Exception\FortniteException;

use Fortnite\Model\TokenModel;

use GuzzleHttp\Middleware;

class Client {

    const EPIC_ACCOUNT_ENDPOINT         = 'https://account-public-service-prod03.ol.epicgames.com/account/api/';
    const EPIC_OAUTH_EXCHANGE_ENDPOINT  = 'https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/exchange';
    const EPIC_OAUTH_VERIFY_ENDPOINT    = 'https://account-public-service-prod03.ol.epicgames.com/account/api/oauth/verify';
    const EPIC_FRIENDS_ENDPOINT         = 'https://friends-public-service-prod06.ol.epicgames.com/friends/api/public/friends/';
    const EPIC_EULA_ENDPOINT            = 'https://eulatracking-public-service-prod-m.ol.epicgames.com/eulatracking/api/public/agreements/fn/';
    const EPIC_EULA_GRANT_ENDPOINT      = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/game/v2/grant_access/';

    const FALLBACK_VERSION_NO = 4;

    private $httpClient;
    private $options;

    private $accessToken;
    private $refreshToken;

    private $accountId;
    private $accountInfo;

    private $challenge;
    private $deviceId;

    private $in_app_id;

    public function __construct($options = [])
    {
        $this->options = $options;

        // Create a random hash to be used for the device ID.
        // This random ID can be overwritten by passing $deviceId to login().
        $this->deviceId = md5(uniqid());

        $this->httpClient = $this->buildHttpClient(
            new FortniteAuthMiddleware($this->deviceId)
        );
    }
    
    /**
     * Login to Fortnite using Epic email and password.
     *
     * @param string $email The email.
     * @param string $password The password.
     * @param string $deviceId The device ID.
     * 
     * The device ID parameter is optional, and should only be used if the account you're logging in with has two factor authentication enabled.
     * If you've logged in with this device token, you won't have to enter 2FA details.
     * 
     * @return void
     */
    public function login(string $email, string $password, string $deviceId = '') : void
    {
        if ($deviceId != '') {
            $this->deviceId = $deviceId;
            
            // If the user passed a custom deviceId, go ahead and update the default HttpClient to use the new deviceId.
            $this->httpClient = $this->buildHttpClient(
                new FortniteAuthMiddleware($this->deviceId)
            );
        }

        try {
            // Get our Epic Launcher authorization token.
            $response = $this->httpClient()->post(self::EPIC_ACCOUNT_ENDPOINT . 'oauth/token', [
                'grant_type'    => 'password',
                'username'      => $email,
                'password'      => $password,
                'includePerms'  => 'false',
                'token_type'    => 'eg1'
            ]);
        } catch (FortniteException $e) {
            if ($e->code() === 'errors.com.epicgames.common.two_factor_authentication.required') {
                $this->challenge = $e->challenge();
                throw new TwoFactorRequiredException();
            }
            throw $e;
        }
        
        $this->httpClient = $this->finalizeLogin($response);

        $this->account()->killSession();

        if (!$this->canPlay()) {
            $this->verifyEula();
        }
    }

    /**
     * Performs two factor authentication after logging in.
     *
     * @param string $code The code from email or authenticator app.
     * @return void
     */
    public function twoFactor(string $code) : void
    {
        if (!$this->challenge) {
            throw new Exception('Two factor challenge has not been set.');
        }

        $response = $this->httpClient()->post(self::EPIC_ACCOUNT_ENDPOINT . 'oauth/token', [
            'grant_type'    =>  'otp',
            'otp'          =>   $code,
            'challenge'     =>  $this->challenge,
            'includePerms'  =>  'false',
            'token_type'    =>  'eg1'
        ]);

        $this->httpClient = $this->finalizeLogin($response);

        $this->account()->killSession();

        if (!$this->canPlay()) {
            $this->verifyEula();
        }
    }
    
    /**
     * Login to Fortnite using a refresh token.
     * 
     * @param string $refreshToken The refresh token.
     * @return void
     */
    public function refresh(string $refreshToken) : void
    {
        $response = $this->httpClient()->post(self::EPIC_ACCOUNT_ENDPOINT . 'oauth/token', [
            'grant_type'    =>  'refresh_token',
            'refresh_token' =>   $refreshToken,
            'includePerms'  =>  'false',
            'token_type'    =>  'eg1'
        ]);

        $this->httpClient = $this->finalizeLogin($response);

        $this->account()->killSession();

        if (!$this->canPlay()) {
            $this->verifyEula();
        }
    }

    /**
     * Checks if the user can play the game.
     * 
     * Used to determine if the EULA should be automatically accepted or not.
     *
     * @return boolean Can the user play?
     */
    private function canPlay() : bool
    {
        $status = $this->status();
        return $status->status() === 'UP' && !empty($status->allowedActions()) && in_array('PLAY', $status->allowedActions());
    }

    /**
     * Automatically verifies EULA for new accounts.
     *
     * @return void
     */
    public function verifyEula() : void
    {
        $data = $this->httpClient()->get(sprintf(self::EPIC_EULA_ENDPOINT . 'account/%s?locale=en-US', $this->accountId()));

        // If for some reason we can't get the latest version, fallback to the latest hardcoded version number.
        $version = $data->version ?? self::FALLBACK_VERSION_NO;

        $this->httpClient()->post(sprintf(self::EPIC_EULA_ENDPOINT .  'version/%d/account/%s/accept?locale=en', $version, $this->accountId()), new \StdClass());

        $this->httpClient()->post(sprintf(self::EPIC_EULA_GRANT_ENDPOINT . '%s', $this->accountId()), new \StdClass(), HttpClient::JSON);
    }

    /**
     * Finalize the Fortnite login.
     * 
     * This method sets some Client properties and creates a new HttpClient that is authorized to make requests to Fortnite's endpoints.
     *
     * @param object $response Response from a Fortnite login request.
     * @return HttpClient Authorized HttpClient.
     */
    private function finalizeLogin(object $response) : HttpClient
    {
        // Set client info.
        $this->accountId = $response->account_id;
        $this->in_app_id = $response->in_app_id ?? "";

        // Set the token info.
        $this->accessToken  = new TokenModel($response->access_token, $response->expires_in, $response->expires_at);
        $this->refreshToken = new TokenModel($response->refresh_token, $response->refresh_expires, $response->refresh_expires_at);

        // Build a new HttpClient with the authorization middleware.
        return $this->buildHttpClient(
            new TokenMiddleware($response->access_token, $response->refresh_token, $response->expires_in, $this->deviceId)
        );
    }

    /**
     * Builds a new HttpClient.
     *
     * @param object $middleware Middleware to be used for the request.
     * @return HttpClient The new HttpClient.
     */
    private function buildHttpClient(object $middleware) : HttpClient
    {
        $handler = \GuzzleHttp\HandlerStack::create();
        $handler->push(Middleware::mapRequest($middleware));

        $newOptions = array_merge(['handler' => $handler], $this->options);

        return new HttpClient(new \GuzzleHttp\Client($newOptions));
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
     * Gets the access token model.
     *
     * @return TokenModel Access token model.
     */
    public function accessToken() : TokenModel
    {
        return $this->accessToken;
    }

    /**
     * Gets the refresh token model.
     *
     * @return TokenModel Refresh token model.
     */
    public function refreshToken() : TokenModel
    {
        return $this->refreshToken;
    }

    /**
     * Gets the user's account ID.
     *
     * @return string Account ID.
     */
    public function accountId() : string
    {
        return $this->accountId;
    }

    /**
     * Gets the in app ID (for leaderboard cohort).
     *
     * @return string In-app ID.
     */
    public function inAppId() : string
    {
        return $this->in_app_id;
    }

    /**
     * Gets the device ID used for two factor authenticated requests.
     * 
     * This Id can be used once you've logged in with it to automatically login even if 2FA is active on your account.
     *
     * @return string Device ID.
     */
    public function deviceId() : string
    {
        return $this->deviceId;
    }

    /**
     * Gets the user's Epic display name
     *
     * @return string Display name.
     */
    public function displayName() : string
    {
        return $this->accountInfo()->displayName;
    }

    /**
     * Gets the account info for the logged in account.
     *
     * @return object Account info.
     */
    public function accountInfo() : object
    {
        if ($this->accountInfo === null) {
            $this->accountInfo = $this->httpClient()->get(sprintf(self::EPIC_ACCOUNT_ENDPOINT . 'public/account/%s', $this->accountId()));
        }

        return $this->accountInfo;
    }

    /**
     * Gets the logged in user's account.
     *
     * @return Account
     */
    public function account() : Account
    {
        return new Account($this);
    }

    /**
     * Gets a user's profile.
     *
     * @param string $username The user's display name.
     * @return Profile
     */
    public function profile(string $username = null) : Profile
    {
        return new Profile($this, $username ?? $this->displayName());
    }

    /**
     * Gets a matchmaking session.
     *
     * @param string $sessionId The session ID.
     * @return Session
     */
    public function session(string $sessionId) : Session
    {
        return new Session($this, $sessionId);
    }

    /**
     * Gets leaderboard information
     *
     * @param string $platform The platform @see Api\Type\Platform
     * @param string $mode The mode @see Api\Type\Mode
     * @return Leaderboard
     */
    public function leaderboards(string $platform, string $mode) : Leaderboard
    {
        return new Leaderboard($this, $platform, $mode);
    }

    /**
     * Gets system files (hotfixes)
     *
     * @return array Array of Api\SystemFile.
     */
    public function systemFiles() : array
    {
        $returnSystemFiles = [];
        
        $files = $this->httpClient()->get(SystemFile::SYSTEM_API);

        foreach ($files as $file) {
            $returnSystemFiles[] = new SystemFile($this, $file);
        }

        return $returnSystemFiles;
    }

    /**
     * Gets Fortnite news.
     *
     * @return News
     */
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

    /**
     * Gets the Fortnite status.
     *
     * @return Status
     */
    public function status() : Status
    {
        return new Status($this);
    }
}