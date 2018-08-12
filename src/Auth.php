<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Profile;
use Fortnite\Status;
use Fortnite\Exception\TwoFactorAuthRequiredException;

class Auth {
    private $access_token;
    private $in_app_id;
    private $refresh_token;
    private $account_id;
    private $expires_in;

    public $profile;



    // TODO: Probably want to lazy load all of these object initializations. Although currently I'm not sure how to go about that with PHP.
    // @Tustin 7/28/2018
    /**
     * Constructs a new Fortnite\Auth instance.
     * @param string $access_token  OAuth2 access token
     * @param string $refresh_token OAuth2 refresh token
     * @param string $account_id    Unreal Engine account id
     * @param string $expires_in    OAuth2 token expiration time
     */
    private function __construct($access_token, $in_app_id, $refresh_token, $account_id, $expires_in) {
        $this->access_token = $access_token;
        $this->in_app_id = $in_app_id;
        $this->refresh_token = $refresh_token;
        $this->account_id = $account_id;
        $this->expires_in = $expires_in;
        $this->profile = new Profile($this->access_token, $this->account_id);
        $this->account = new Account($this->access_token);
        $this->leaderboard  = new Leaderboard($this->access_token, $this->in_app_id, $this->account);
        $this->store = new Store($this->access_token);
        $this->news = new News($this->access_token);
        $this->status = new Status($this->access_token);
    }

    /**
     * Login using Unreal Engine credentials to access Fortnite API.
     *
     * @param      string     $email     The account email
     * @param      string     $password  The account password
     *
     * @throws     Exception  Throws exception on API response errors (might get overridden by Guzzle exceptions)
     *
     * @return     self       New Auth instance
     */
    public static function login($email, $password, $challenge = '', $code = '') {

        $requestParams = [
            'includePerms' => 'false', // We don't need these here
            'token_type' => 'eg1'
        ];

        if (empty($challenge) && empty($code)) {
            // Regular login
            $requestParams = array_merge($requestParams, [
                'grant_type' => 'password',
                'username' => $email,
                'password' => $password,
            ]);
        } else {
            $requestParams = array_merge($requestParams, [
                'grant_type' => 'otp',
                'otp' => $code,
                'challenge' => $challenge,
            ]);
        }

        // First, we need to get a token for the Unreal Engine client
        $data = FortniteClient::sendUnrealClientPostRequest(FortniteClient::EPIC_OAUTH_TOKEN_ENDPOINT, $requestParams);

        if (!isset($data->access_token)) {
            if ($data->errorCode === 'errors.com.epicgames.common.two_factor_authentication.required') {
                throw new TwoFactorAuthRequiredException($data->challenge);
            }

            throw new \Exception($data->errorMessage);
        }

        // Now that we've got our Unreal Client launcher token, let's get an exchange token for Fortnite
        $data = FortniteClient::sendUnrealClientGetRequest(FortniteClient::EPIC_OAUTH_EXCHANGE_ENDPOINT, $data->access_token, true);

        if (!isset($data->code)) {
            throw new \Exception($data->errorMessage);
        }

        // Should be good. Let's get our tokens for the Fortnite API
        $data = FortniteClient::sendUnrealClientPostRequest(FortniteClient::EPIC_OAUTH_TOKEN_ENDPOINT, [
            'grant_type' => 'exchange_code',
            'exchange_code' => $data->code,
            'includePerms' => false, // We don't need these here
            'token_type' => 'eg1'
        ], FortniteClient::FORTNITE_AUTHORIZATION);

        if (!isset($data->access_token)) {
            throw new \Exception($data->errorMessage);
        }

        return new self($data->access_token, $data->in_app_id, $data->refresh_token, $data->account_id, $data->expires_in);
    }

    /**
     * Refreshes OAuth2 tokens using an existing refresh token.
     * @param  string $refresh_token Exisiting OAuth2 refresh token
     * @return self                New Auth instance
     */
    public static function refresh($refresh_token) {
        $data = FortniteClient::sendUnrealClientPostRequest(FortniteClient::EPIC_OAUTH_TOKEN_ENDPOINT, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'includePerms' => "false", // We don't need these here
            'token_type' => 'eg1',
        ], FortniteClient::FORTNITE_AUTHORIZATION);

        if (!$data->access_token) {
            throw new \Exception($data->errorMessage);
        }

       return new self($data->access_token, $data->in_app_id, $data->refresh_token, $data->account_id, $data->expires_in);
    }

    /**
     * Returns current refresh token.
     * @return string OAuth2 refresh token
     */
    public function refreshToken() {
        return $this->refresh_token;
    }

    /**
     * Returns the time until the OAuth2 access token expires.
     * @return string Time until OAuth2 access token expires (in seconds)
     */
    public function expiresIn() {
        return $this->expires_in;
    }

    /**
     * Returns current access token.
     * @return string OAuth2 access token
     */
    public function accessToken() {
        return $this->access_token;
    }

    public function inAppId() {
        return $this->in_app_id;
    }
}