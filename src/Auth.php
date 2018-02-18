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

    public function __construct($email, $password) {
        $result = self::login($email, $password);
        $this->access_token = $result->access_token;
        $this->refresh_token = $result->refresh_token;
        $this->account_id = $result->account_id;
        $this->expires_at = $result->expires_at;
        $this->profile = new Profile($this->access_token, $this->account_id);
    }

    private static function login($email, $password) {
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

        return $data;
    }
}