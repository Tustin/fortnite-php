<?php
namespace Fortnite;

use Fortnite\FortniteClient;
use Fortnite\Profile;

class Auth {
    private $access_token;
    private $refresh_token;
    private $account_id;
    private $expires_at;

    public $profile;


    /**
     * Constructs a new Authentication instance.
     * @param string $access_token  OAuth2 access token
     * @param string $refresh_token OAuth2 refresh token
     * @param string $account_id    Unreal Engine account id
     * @param string $expires_at    OAuth2 token expiration time
     */
    private function __construct($access_token, $refresh_token, $account_id, $expires_at) {
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->account_id = $account_id;
        $this->expires_at = $expires_at;
        $this->profile = new Profile($this->access_token, $this->account_id);
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
    public static function login($email, $password) {
        // First, we need to get a token for the Unreal Engine client
        $data = FortniteClient::sendUnrealClientPostRequest(FortniteClient::EPIC_OAUTH_TOKEN_ENDPOINT, [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
            'includePerms' => false, // We don't need these here
            'token_type' => 'eg1'
        ]);

        if (!$data->access_token) {
            throw new Exception($data->errorMessage);
        }

        // Now that we've got our Unreal Client launcher token, let's get an exchange token for Fortnite
        $data = FortniteClient::sendUnrealClientGetRequest(FortniteClient::EPIC_OAUTH_EXCHANGE_ENDPOINT, $data->access_token, true);

        if (!$data->code) {
            throw new Exception($data->errorMessage);
        }

        // Should be good. Let's get our tokens for the Fortnite API
        $data = FortniteClient::sendUnrealClientPostRequest(FortniteClient::EPIC_OAUTH_TOKEN_ENDPOINT, [
            'grant_type' => 'exchange_code',
            'exchange_code' => $data->code,
            'includePerms' => false, // We don't need these here
            'token_type' => 'eg1'
        ], FortniteClient::FORTNITE_AUTHORIZATION);

        if (!$data->access_token) {
            throw new Exception($data->errorMessage);
        }

        return new self($data->access_token, $data->refresh_token, $data->account_id, $data->expires_at);
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
            throw new Exception($data->errorMessage);
        }

       return new self($data->access_token, $data->refresh_token, $data->account_id, $data->expires_at);
    }

    /**
     * Returns current refresh token.
     * @return string OAuth2 refresh token
     */
    public function getRefreshToken() {
        return $this->refresh_token;
    }
}